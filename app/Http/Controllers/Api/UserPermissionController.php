<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Actions\Role\Assign as AssignRoleAction;
use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\ArchiveUser\Attach as AttachArchiveUserAction;
use App\Actions\ArchiveUser\Sync as SyncArchiveUserAction;
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

  public function store(Request $request, User $user): UserResource
  {
    $archive = (new FindArchiveAction())->execute($request->archive, true);

    if ($archive) {
      (new AssignRoleAction())->execute($user, $archive->id, $request->role);
      (new AttachArchiveUserAction())->execute($user, $archive->id, $request->role);
      (new StoreUserPermissionAction())->execute($user, $archive->id, $request->permissions);
    }
    return new UserResource($user);
  }
  
  public function update(Request $request, User $user): UserResource
  {
    $archive = (new FindArchiveAction())->execute($request->archive, true);

    if ($archive) {
      (new AssignRoleAction())->execute($user, $archive->id, $request->role);
      (new SyncArchiveUserAction())->execute($user, $archive->id, $request->role);
      (new StoreUserPermissionAction())->execute($user, $archive->id, $request->permissions,);
    }
    
    return new UserResource($user);
  }
}
