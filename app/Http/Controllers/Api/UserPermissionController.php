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
    (new AssignRoleAction())->execute($user, $request->role);
    
    (new StoreUserPermissionAction())->execute($user, [
        'archives' => $request->archives,
        'selectedPermissions' => $request->selectedPermissions
      ]
    );

    foreach($request->archives as $archive)
    {
      $archive = (new FindArchiveAction())->execute($archive, true);

      if ($archive) {
        (new AttachArchiveUserAction())->execute($user, $request->role, [$archive]);
      }

    }

    return new UserResource($user);
  }

}
