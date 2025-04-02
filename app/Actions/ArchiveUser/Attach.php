<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Attach
{
  public function execute(User $user, int $archiveId, int $roleId = NULL): null|array
  {
    // Detach any existing entry for this archive
    $user->archives()->detach($archiveId);

    // Attach without metadata if the user is 'Super Admin' or 'Admin'
    if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
      return $user->archives()->attach($archiveId);
    }

    // Attach with role and metadata for regular users
    return $user->archives()->attach($archiveId, [
      'role_id' => $roleId,
      'added_by' => auth()->id(),
      'added_at' => now(),
    ]);
  }
}