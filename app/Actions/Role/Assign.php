<?php
namespace App\Actions\Role;
use App\Models\User;

class Assign
{
  /**
   * Give the user exactly one role for the given archive (NULL = account-wide).
   * Roles the user holds for other archives stay untouched.
   */
  public function execute(User $user, ?int $archiveId, int $roleId): void
  {
    $roles = $user->roles();
    $archiveId === null
      ? $roles->wherePivotNull('archive_id')->detach()
      : $roles->wherePivot('archive_id', $archiveId)->detach();

    $user->roles()->attach($roleId, ['archive_id' => $archiveId]);
    $user->unsetRelation('roles');
    $user->forgetCachedPermissions();
  }
}
