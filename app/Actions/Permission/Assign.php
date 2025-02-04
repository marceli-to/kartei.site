<?php
namespace App\Actions\Permission;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class Assign
{
  public function execute(User $user, Permission $permission): User
  {
    return $user->givePermissionTo($permission->name);
  }
}
