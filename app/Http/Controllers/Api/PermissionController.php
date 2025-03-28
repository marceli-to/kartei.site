<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
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

  public function getByUser(Request $request, User $user): JsonResponse
  {
    $permissions = (new GetPermissionAction())->execute(
      [
        'user' => $user->id,
        'hidden' => true,
        'group_by_archive_id' => true,
      ]
    );
    return response()->json($permissions);
  }
}