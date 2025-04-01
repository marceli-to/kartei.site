<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Sync
{
  public function execute(User $user, int $archiveId, int $roleId): null|array
  {
    // Detach any existing relationship with this archive
    $user->archives()->detach($archiveId);

    // Sync with new pivot data
    return $user->archives()->syncWithoutDetaching([
      $archiveId => [
        'role_id' => $roleId,
        'added_by' => auth()->id(),
        'added_at' => now(),
      ]
    ]);
  }
}