<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;
use App\Models\Archive;

class Attach
{
  public function execute(User $user, Archive $archive): array
  {
    return $user->archives()->syncWithoutDetaching([$archive->id]);
  }
}
