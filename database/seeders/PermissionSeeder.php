<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use \App\Models\User;

class PermissionSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // create a role for 'super admin'
    $superAdminRole = Role::create([
      'name' => 'Super Admin',
      'guard_name' => 'web'
    ]);

    // assign super admin role to user 'm@marceli.to'
    $user = User::where('email', 'm@marceli.to')->first();
    $user->roles()->sync([$superAdminRole->id]);

    // create a role for 'admin'
    $adminRole = Role::create([
      'name' => 'Admin',
      'guard_name' => 'web'
    ]);

    // create a role for 'manager'
    $managerRole = Role::create([
      'name' => 'Manager',
      'guard_name' => 'web'
    ]);

    // create a role for 'archivist'
    $archivistRole = Role::create([
      'name' => 'Archivist',
      'guard_name' => 'web'
    ]);

    // create a role for 'viewer'
    $viewerRole = Role::create([
      'name' => 'Viewer',
      'guard_name' => 'web'
    ]);

    // create global permissions
    $globalPermissions = [
      'use.favorites',
      'use.exports',
      'use.sharing',
      'use.themes',
      'view.archives',
      'view.records',
    ];

    foreach ($globalPermissions as $globalPermission)
    {
      $p = Permission::create([
        'name' => $globalPermission,
        'guard_name' => 'web'
      ]);

      // assign permission to role
      $adminRole->givePermissionTo($p);
      $managerRole->givePermissionTo($p);
      $archivistRole->givePermissionTo($p);
      $viewerRole->givePermissionTo($p);
    }
  }
}
