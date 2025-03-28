<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Attach
{
  public function execute(User $user, int $roleId, int $archiveId): null|array
  {
    // Detach any existing entry for this archive
    $user->archives()->detach($archiveId);

    // Attach with the new role and metadata
    return $user->archives()->attach($archiveId, [
      'role_id' => $roleId,
      'added_by' => auth()->id(),
      'added_at' => now(),
    ]);
  }
}