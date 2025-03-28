<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Actions\Permission\Get as GetPermissionAction;
use App\Actions\Role\Get as GetRolesAction;
use App\Models\User;

class UserController extends Controller
{
  public function index()
  {

    // // Get permissions for 'Admin'
    // $adminPermissions = collect($permissions)->filter(function ($permission) {
    //   return in_array('Archivist', $permission['belongs_to']);
    // });
    // dd($adminPermissions);

    // by user
    $userId = 3;
    $permissionsUser = (new GetPermissionAction())->execute(
      [
        'user' => $userId,
        'hidden' => true,
        'group_by_archive_id' => true,
        // 'group_by_key' => true,
        // 'ordered' => true,
        // 'publish_only' => true
      ]
    );

    dd($permissionsUser);

    // filter out the permissions where publish is true
    // $permissionsUser = $permissionsUser->filter(function ($permission) {
    //   return !$permission->publish;
    // });

    foreach ($permissionsUser as $permissionGroup) {
      echo "Group<br>";
      foreach ($permissionGroup as $permission) {
        echo $permission->name . "<br>";
      }
    }
    die();
    dd($permissionsUser);

    // // by role
    // $permissionsRole = (new GetPermissionAction())->execute(
    //   [
    //     'role' => 'Admin',
    //     'group_by_key' => true,
    //     'ordered' => true,
    //     'publish_only' => true
    //   ]
    // );

    // dd($permissionsUser, $permissionsRole);

    // by role
    $roles = (new GetRolesAction())->execute(
      [
        'with_permissions' => true,
        'permission_constraints' => [
          'publish_only' => true,
          'ordered' => true,
          'group_by_key' => true
        ]
      ]
    );

    dd($roles);

  }

}