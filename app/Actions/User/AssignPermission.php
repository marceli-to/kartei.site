<?php
namespace App\Actions\User;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AssignPermission
{
  public function execute(User $user, Permission $permission): User
  {
    return $user->givePermissionTo($permission->name);
  }
}
