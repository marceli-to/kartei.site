<?php
namespace App\Actions\ArchiveUser;
use App\Models\Archive;

class Get
{
  public function execute(Archive $archive, $filter = FALSE)
  {
    // Only this archive's role, plus account-wide roles (Admin, Super Admin) for the filter below
    $users = $archive->users()->with(['roles' => fn ($query) => $query
      ->where(fn ($query) => $query
        ->where('model_has_roles.archive_id', $archive->id)
        ->orWhereNull('model_has_roles.archive_id'))
    ])->get();

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