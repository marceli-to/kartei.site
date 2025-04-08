<?php
namespace App\Actions\ArchiveUser;
use App\Models\Archive;

class Get
{
  public function execute(Archive $archive, $filter = FALSE)
  {
    $users = $archive->users()->get();

    // Return all users if filter is FALSE
    if (!$filter) {
      return $users;
    }
    
    // Filter out 'Super Admin' and 'Admin' roles if filter is TRUE
    $filteredUsers = $users->filter(function ($user) {
      return !$user->hasRole('Super Admin') && !$user->hasRole('Admin');
    });

    return $filteredUsers;
  }
}