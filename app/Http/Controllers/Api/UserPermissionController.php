<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\Role\Assign as AssignRoleAction;
use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\ArchiveUser\Sync as SyncArchiveUserAction;
use App\Actions\UserPermission\Store as StoreUserPermissionAction;
use App\Http\Resources\UserPermissionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\UserPermission\StoreRequest;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends Controller
{
  public function get(Request $request): UserPermissionResource
  {
    return new UserPermissionResource($request->user());
  }

  /**
   * Give the user a role and permissions on one archive, adding them
   * to the archive if they are not a member yet.
   */
  public function store(StoreRequest $request, User $user): UserResource
  {
    $archive = (new FindArchiveAction())->execute($request->input('archive'), true);
    $this->authorize('assignToArchive', [$user, $archive]);

    DB::transaction(function () use ($user, $archive, $request) {
      (new AssignRoleAction())->execute($user, $archive->id, $request->integer('role'));
      (new SyncArchiveUserAction())->execute($user, $archive->id, $request->integer('role'));
      (new StoreUserPermissionAction())->execute($user, $archive->id, $request->input('permissions', []));
    });

    return new UserResource($user);
  }
}
