<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Role\Assign as AssignRoleAction;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserAssignRole extends Command
{
  protected $signature = 'user:assign:role';

  protected $description = 'Test the user role assignment action';

  public function handle()
  {
    $users = User::all()->pluck('email', 'id')->toArray();
    $choice = $this->choice('Which user do you want to add a role to?', $users);
    $userId = array_search($choice, $users);
    $user = User::find($userId);

    $roles = Role::all()->pluck('name', 'id')->toArray();
    $choice = $this->choice('Which role do you want to add to this user?', $roles);
    $roleId = array_search($choice, $roles);
    $role = Role::find($roleId);

    (new AssignRoleAction())->execute($user, $role->id);
    $this->info('Role ' . $role->name . ' assigned to user ' . $user->email);
  }
}