<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;
use App\Models\Archive;

class Detach
{
  public function execute(User $user, Archive $archive): int
  {
    return $user->archives()->detach([$archive->id]);
  }
}
