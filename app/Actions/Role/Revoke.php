<?php
namespace App\Actions\Role;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Revoke
{
  public function execute(User $user, Role $role): User
  {
    return $user->removeRole($role->name);
  }
}
