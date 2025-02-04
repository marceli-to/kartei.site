<?php
namespace App\Actions\User;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class RevokePermission
{
  public function execute(User $user, Permission $permission): User
  {
    return $user->revokePermissionTo($permission);
  }
}
