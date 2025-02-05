<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Detach
{
  public function execute(User $user, $archives): int
  {
    return $user->archives()->detach($archives);
  }
}
