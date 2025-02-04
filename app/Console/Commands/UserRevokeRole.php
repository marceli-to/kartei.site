<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Role\Revoke as RevokeRoleAction;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRevokeRole extends Command
{
    protected $signature = 'user:revoke:role';

    protected $description = 'Revoke a role from a user';

    public function handle()
    {
      $users = User::all()->pluck('email', 'id')->toArray();
      $choice = $this->choice('Which user do you want to revoke a role from?', $users);
      $userId = array_search($choice, $users);
      $user = User::find($userId);

      // Get roles for this user
      $roles = $user->roles()->pluck('name', 'id')->toArray();
      $choice = $this->choice('Which role do you want to revoke from this user?', $roles);
      $roleId = array_search($choice, $roles);
      $role = Role::find($roleId);

      (new RevokeRoleAction())->execute($user, $role);
      $this->info('Role ' . $role->name . ' revoked from user ' . $user->email);
    }
}
