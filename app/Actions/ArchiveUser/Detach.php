<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Detach
{
  public function execute(User $user, $archives): int
  {
    $detached = $user->archives()->detach($archives);
    $user->flushArchiveMemberships();
    return $detached;
  }
}
