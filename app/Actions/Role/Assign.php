<?php
namespace App\Actions\Role;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Assign
{
  public function execute(User $user, $archiveId = NULL, $roleId): array
  {
    // First, find any roles the user has for this archive
    $existingRoles = $user->roles()
      ->wherePivot('archive_id', $archiveId)
      ->get();

    // Detach them
    foreach ($existingRoles as $role) {
      $user->roles()->detach($role->id);
    }

    // Now attach the new one
    $pivotData = [
      $roleId => ['archive_id' => $archiveId]
    ];

    return $user->roles()->syncWithoutDetaching($pivotData);
  }
}
