<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Archive;
use App\Actions\Permission\Get as GetPermissionAction;

class PermissionController extends Controller
{
  public function get(Request $request): JsonResponse
  {
    $permissions = (new GetPermissionAction())->execute([
      'publish_only' => true,
      'ordered' => true,
      'group_by_key' => true
    ]);

    return response()->json($permissions);
  }

  public function getByRole(Request $request, string $role): JsonResponse
  {
    $permissions = (new GetPermissionAction())->execute([
      'role' => $role,
      'publish_only' => true,
      'ordered' => true,
      'group_by_key' => false
    ]);

    return response()->json($permissions);
  }

  /**
   * The user's role and permissions per archive, keyed by archive uuid:
   * { "<uuid>": { "role": 4, "permissions": [11, 12] } }.
   * Permission ids are those of the published base permissions.
   * Only archives the actor administers are included.
   */
  public function getByUser(Request $request, User $user): JsonResponse
  {
    $this->authorize('view', $user);
    $actor = $request->user();

    $baseIds = Permission::where('publish', true)->pluck('id', 'name');
    $granted = $user->permissions->pluck('name');

    $archives = $user->archives()
      ->wherePivotNotNull('role_id')
      ->get()
      ->filter(fn (Archive $archive) => $actor->isSuperAdmin() || $actor->isAdminOf($archive))
      ->mapWithKeys(fn (Archive $archive) => [$archive->uuid => [
        'role' => (int) $archive->pivot->role_id,
        'permissions' => $granted
          ->filter(fn (string $name) => str_ends_with($name, '.' . $archive->id))
          ->map(fn (string $name) => $baseIds[substr($name, 0, strrpos($name, '.'))] ?? null)
          ->filter()
          ->values(),
      ]]);

    return response()->json((object) $archives->all());
  }
}
