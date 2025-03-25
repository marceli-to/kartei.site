<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;

class Attach
{
  public function execute(User $user, int $role, array $archives): null
  {
    return $user->archives()->attach($archives, [
      'role_id' => $role, 
      'added_by' => auth()->user()->id, 
      'added_at' => now()
    ]);
  }
}
