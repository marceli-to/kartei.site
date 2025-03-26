<?php
namespace App\Actions\ArchiveUser;
use App\Models\User;
use App\Models\Archive;

class Attach
{
  public function execute(User $user, int $role, Archive $archive): null
  {
    return $user->archives()->attach($archive->id, [
      'role_id' => $role, 
      'added_by' => auth()->user()->id, 
      'added_at' => now()
    ]);
  }
}