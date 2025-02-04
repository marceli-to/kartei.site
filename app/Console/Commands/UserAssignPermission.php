<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Permission\Assign as AssignPermissionAction;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class UserAssignPermission extends Command
{
  protected $signature = 'user:assign:permission';

  protected $description = 'Test the user permission assignment action';

  public function handle()
  {
    $users = User::all()->pluck('email', 'id')->toArray();
    $choice = $this->choice('Which user do you want to add a permission to?', $users);
    $userId = array_search($choice, $users);
    $user = User::find($userId);

    $permissions = Permission::all()->pluck('name', 'id')->toArray();
    $choice = $this->choice('Which permission do you want to add to this user?', $permissions);
    $permissionId = array_search($choice, $permissions);
    $permission = Permission::find($permissionId);

    (new AssignPermissionAction())->execute($user, $permission);
    $this->info('Permission ' . $permission->name . ' assigned to user ' . $user->email);
  }
}