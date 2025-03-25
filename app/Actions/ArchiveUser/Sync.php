<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Sync
{
  public function execute(User $user, array $archives): array
  {
    return $user->archives()->syncWithoutDetaching($archives);
  }
}
