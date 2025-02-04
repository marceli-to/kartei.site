<?php
namespace App\Actions\Permission;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class Revoke
{
  public function execute(User $user, Permission $permission): User
  {
    return $user->revokePermissionTo($permission);
  }
}
