<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Attach
{
  public function execute(User $user, int $archiveId, int $roleId = NULL): null|array
  {
    // Detach any existing entry for this archive
    $user->archives()->detach($archiveId);

    // Attach with the new role and metadata, but only if the user is not 'Super Admin' or 'Admin'
    if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
      return $user->archives()->attach($archiveId);
    }

    // Add role and metadata for regular users
    return $user->archives()->attach($archiveId, [
      'role_id' => $roleId,
      'added_by' => auth()->id(),
      'added_at' => now(),
    ]);
  }
}