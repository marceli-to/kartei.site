<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Actions\Role\Assign as AssignRoleAction;
use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\ArchiveUser\Attach as AttachArchiveUserAction;
use App\Actions\UserPermission\Store as StoreUserPermissionAction;
use App\Actions\Permission\Get as GetPermissionAction;
use App\Http\Resources\UserPermissionResource;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserPermissionController extends Controller
{
  public function get(Request $request): UserPermissionResource
  {
    return new UserPermissionResource($request->user());
  }

  public function store(Request $request, User $user)
  {
    // Set role for the user
    (new AssignRoleAction())->execute($user, $request->role);

    // Store permissions for this archive
    (new StoreUserPermissionAction())->execute($user, [
      'archive' => $request->archive,
      'selectedPermissions' => $request->permissions
    ]);

    // Find and attach archive to user
    $archive = (new FindArchiveAction())->execute($request->archive, true);
    if ($archive) {
      (new AttachArchiveUserAction())->execute($user, $request->role, $archive);
    }
    
    return new UserResource($user);
  }
}
