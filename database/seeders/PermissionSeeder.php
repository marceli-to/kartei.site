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

    // assign super admin role to user 'm@marceli.to'
    $user2 = User::where('email', 'bf@wbg.ch')->first();
    $user2->roles()->sync([$superAdminRole->id]);

    // create a role for 'Administrator/in'
    $adminRole = Role::create([
      'name' => 'Admin',
      'guard_name' => 'web'
    ]);

    // create a role for 'Verwalter/in'
    $managerRole = Role::create([
      'name' => 'Manager',
      'guard_name' => 'web'
    ]);

    // create a role for 'Archivar/in'
    $archivistRole = Role::create([
      'name' => 'Archivist',
      'guard_name' => 'web'
    ]);

    // create a role for 'Betrachter/in'
    $viewerRole = Role::create([
      'name' => 'Viewer',
      'guard_name' => 'web'
    ]);

    // Permissions
    $permissions = [
      [
        'name' => 'create.archive',
        'display_name' => 'Erstellen',
        'group_key' => 'archive',
        'group_name' => 'Kartei',
        'order' => 1,
        'belongs_to' => ['Admin'],
        'publish' => 0
      ],
      [
        'name' => 'edit.archive',
        'display_name' => 'Bearbeiten',
        'group_key' => 'archive',
        'group_name' => 'Kartei',
        'order' => 2,
        'belongs_to' => ['Admin', 'Manager'],
        'publish' => 1
      ],
      [
        'name' => 'delete.archive',
        'display_name' => 'Löschen',
        'group_key' => 'archive',
        'group_name' => 'Kartei',
        'order' => 3,
        'belongs_to' => ['Admin', 'Manager'],
        'publish' => 1
      ],
      [
        'name' => 'edit.basic_info',
        'display_name' => 'Basisinformationen',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 4,
        'belongs_to' => ['Admin', 'Manager'],
        'publish' => 1
      ],
      [
        'name' => 'edit.structure',
        'display_name' => 'Struktur',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 5,
        'belongs_to' => ['Admin', 'Manager'],
        'publish' => 1
      ],      
      [
        'name' => 'edit.template',
        'display_name' => 'Kartenvorlage',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 6,
        'belongs_to' => ['Admin', 'Manager'],
        'publish' => 1
      ],
      [
        'name' => 'edit.tags',
        'display_name' => 'Tags',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 7,
        'belongs_to' => ['Admin', 'Manager'],
        'publish' => 1
      ],
      [
        'name' => 'edit.users',
        'display_name' => 'Benutzer',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 8,
        'belongs_to' => ['Admin'],
        'publish' => 0
      ],
      [
        'name' => 'export.card',
        'display_name' => 'Exportieren',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 9,
        'belongs_to' => ['Admin', 'Manager', 'Archivist', 'Viewer'],
        'publish' => 1
      ],
      [
        'name' => 'share.card',
        'display_name' => 'Teilen',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 10,
        'belongs_to' => ['Admin', 'Manager', 'Archivist', 'Viewer'],
        'publish' => 1
      ],
      [
        'name' => 'create.card',
        'display_name' => 'Erstellen',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 11,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'publish' => 1
      ],
      [
        'name' => 'edit.card',
        'display_name' => 'Bearbeiten',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 12,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'publish' => 1
      ],
      [
        'name' => 'delete.card',
        'display_name' => 'Löschen',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 13,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'publish' => 1
      ],
      [
        'name' => 'manage.order',
        'display_name' => 'Ordnung',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 14,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'publish' => 1
      ],
      [
        'name' => 'manage.images',
        'display_name' => 'Bilder',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 15,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'publish' => 1
      ],
      [
        'name' => 'manage.description',
        'display_name' => 'Beschreibung',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 16,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'publish' => 1
      ],
      [
        'name' => 'manage.favorites',
        'display_name' => 'Favoriten',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 17,
        'belongs_to' => ['Admin', 'Manager', 'Archivist', 'Viewer'],
        'publish' => 1
      ],
      [
        'name' => 'edit.address',
        'order' => 18,
        'belongs_to' => ['Admin'],
        'publish' => 0
      ],
      [
        'name' => 'edit.subscription',
        'order' => 19,
        'belongs_to' => ['Admin'],
        'publish' => 0
      ],
      [
        'name' => 'edit.clients',
        'order' => 20,
        'belongs_to' => ['Admin'],
        'publish' => 0
      ]
    ];

    // Create all permissions
    foreach ($permissions as $permission) {
      $p = Permission::create([
        'name' => $permission['name'],
        'guard_name' => 'web',
        'display_name' => $permission['display_name'] ?? null,
        'group_key' => $permission['group_key'] ?? null,
        'group_name' => $permission['group_name'] ?? null,
        'order' => $permission['order'],
        'publish' => $permission['publish']
      ]);

      // Assign permissions to roles

      // Super Admin
      $superAdminRole->givePermissionTo($p);

      // Admin
      if (in_array('Admin', $permission['belongs_to'])) {
        $adminRole->givePermissionTo($p);
      }

      // Manager
      if (in_array('Manager', $permission['belongs_to'])) {
        $managerRole->givePermissionTo($p);
      }

      // Archivist
      if (in_array('Archivist', $permission['belongs_to'])) {
        $archivistRole->givePermissionTo($p);
      }

      // Viewer
      if (in_array('Viewer', $permission['belongs_to'])) {
        $viewerRole->givePermissionTo($p);
      }
    }
  }
}
