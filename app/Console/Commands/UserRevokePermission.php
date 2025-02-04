<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\User\RevokePermission as RevokePermissionAction;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class UserRevokePermission extends Command
{
    protected $signature = 'user:revoke:permission';

    protected $description = 'Revoke a permission from a user';

    public function handle()
    {
      $users = User::all()->pluck('email', 'id')->toArray();
      $choice = $this->choice('Which user do you want to revoke a permission from?', $users);
      $userId = array_search($choice, $users);
      $user = User::find($userId);

      // Get permissions for this user
      $permissions = $user->permissions()->pluck('name', 'id')->toArray();
      $choice = $this->choice('Which permission do you want to revoke from this user?', $permissions);
      $permissionId = array_search($choice, $permissions);
      $permission = Permission::find($permissionId);

      (new RevokePermissionAction())->execute($user, $permission);
      $this->info('Permission ' . $permission->name . ' revoked from user ' . $user->email);
    }
}
