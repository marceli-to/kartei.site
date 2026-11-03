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
use App\Models\Archive;
use App\Http\Requests\UserPermission\StoreRequest;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends Controller
{
  public function get(Request $request): UserPermissionResource
  {
    return new UserPermissionResource($request->user());
  }

  public function store(StoreRequest $request, User $user): UserResource
  {
    $archive = $this->authorizedArchive($request, $user);

    DB::transaction(function () use ($user, $archive, $request) {
      (new AssignRoleAction())->execute($user, $archive->id, $request->integer('role'));
      (new AttachArchiveUserAction())->execute($user, $archive->id, $request->integer('role'));
      (new StoreUserPermissionAction())->execute($user, $archive->id, $request->input('permissions', []));
    });

    return new UserResource($user);
  }

  public function update(StoreRequest $request, User $user): UserResource
  {
    $archive = $this->authorizedArchive($request, $user);

    DB::transaction(function () use ($user, $archive, $request) {
      (new AssignRoleAction())->execute($user, $archive->id, $request->integer('role'));
      (new SyncArchiveUserAction())->execute($user, $archive->id, $request->integer('role'));
      (new StoreUserPermissionAction())->execute($user, $archive->id, $request->input('permissions', []));
    });

    return new UserResource($user);
  }

  private function authorizedArchive(StoreRequest $request, User $user): Archive
  {
    $archive = (new FindArchiveAction())->execute($request->input('archive'), true);
    $this->authorize('assignToArchive', [$user, $archive]);
    return $archive;
  }
}
