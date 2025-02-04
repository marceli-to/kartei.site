<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;
use App\Models\Archive;

class Sync
{
  public function execute(User $user, Archive $archive): array
  {
    return $user->archives()->sync([$archive->id]);
  }
}
