<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Actions\Role\Assign as AssignRoleAction;
use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\ArchiveUser\Attach as AttachArchiveUserAction;
use App\Actions\UserPermission\Store as StoreUserPermissionAction;
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
    // Get the permissions data from the request
    $permissions = $request->permissions;
    
    // Process each archive's permissions separately
    foreach ($permissions as $permission) {
      // Set role for this archive
      (new AssignRoleAction())->execute($user, $permission['role']);
      
      // Store permissions for this archive
      (new StoreUserPermissionAction())->execute($user, [
        'archive' => $permission['archive'],
        'selectedPermissions' => $permission['selectedPermissions']
      ]);
      
      // Find and attach archive to user
      $archive = (new FindArchiveAction())->execute($permission['archive'], true);
      
      if ($archive) {
        (new AttachArchiveUserAction())->execute($user, $permission['role'], $archive);
      }
    }
    
    return new UserResource($user);
  }
}
