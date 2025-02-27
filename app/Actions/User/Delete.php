<?php
namespace App\Actions\User;
use App\Models\User;

class Delete
{
  public function execute(User $user): bool
  {
    // remove roles
    $user->roles()->detach();

    // remove permissions
    $user->permissions()->detach();

    // remove archives
    $user->archives()->detach();

    return $user->delete();
  }
}
