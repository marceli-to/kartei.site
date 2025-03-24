<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Actions\User\Create as CreateAction;

class UserController extends Controller
{
  public function index()
  {
    $permissions = [
      [
        'name' => 'create.archive',
        'display_name' => 'Erstellen',
        'group_key' => 'archive',
        'group_name' => 'Kartei',
        'order' => 1,
        'belongs_to' => ['Admin'],
        'visible' => 0
      ],
      [
        'name' => 'edit.archive',
        'display_name' => 'Erstellen',
        'group_key' => 'archive',
        'group_name' => 'Kartei',
        'order' => 2,
        'belongs_to' => ['Admin', 'Manager'],
        'visible' => 1
      ],
      [
        'name' => 'delete.archive',
        'display_name' => 'Löschen',
        'group_key' => 'archive',
        'group_name' => 'Kartei',
        'order' => 3,
        'belongs_to' => ['Admin', 'Manager'],
        'visible' => 1
      ],
      [
        'name' => 'edit.basic_info',
        'display_name' => 'Basisinformationen',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 4,
        'belongs_to' => ['Admin', 'Manager'],
        'visible' => 1
      ],
      [
        'name' => 'edit.structure',
        'display_name' => 'Struktur',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 5,
        'belongs_to' => ['Admin', 'Manager'],
        'visible' => 1
      ],      
      [
        'name' => 'edit.template',
        'display_name' => 'Kartenvorlage',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 6,
        'belongs_to' => ['Admin', 'Manager'],
        'visible' => 1
      ],
      [
        'name' => 'edit.tags',
        'display_name' => 'Tags',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 7,
        'belongs_to' => ['Admin', 'Manager'],
        'visible' => 1
      ],
      [
        'name' => 'edit.users',
        'display_name' => 'Benutzer',
        'group_key' => 'archive_edit',
        'group_name' => 'Bearbeiten',
        'order' => 8,
        'belongs_to' => ['Admin'],
        'visible' => 0
      ],
      [
        'name' => 'create.card',
        'display_name' => 'Erstellen',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 9,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'visible' => 1
      ],
      [
        'name' => 'edit.card',
        'display_name' => 'Bearbeiten',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 10,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'visible' => 1
      ],
      [
        'name' => 'delete.card',
        'display_name' => 'Löschen',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 11,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'visible' => 1
      ],
      [
        'name' => 'export.card',
        'display_name' => 'Exportieren',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 12,
        'belongs_to' => ['Admin', 'Manager', 'Archivist', 'Viewer'],
        'visible' => 1
      ],
      [
        'name' => 'share.card',
        'display_name' => 'Teilen',
        'group_key' => 'card',
        'group_name' => 'Karten',
        'order' => 13,
        'belongs_to' => ['Admin', 'Manager', 'Archivist', 'Viewer'],
        'visible' => 1
      ],
      [
        'name' => 'manage.order',
        'display_name' => 'Ordnung',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 14,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'visible' => 1
      ],
      [
        'name' => 'manage.tags',
        'display_name' => 'Tags',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 15,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'visible' => 1
      ],
      [
        'name' => 'manage.images',
        'display_name' => 'Bilder',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 16,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'visible' => 1
      ],
      [
        'name' => 'manage.description',
        'display_name' => 'Beschreibung',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 17,
        'belongs_to' => ['Admin', 'Manager', 'Archivist'],
        'visible' => 1
      ],
      [
        'name' => 'manage.favorites',
        'display_name' => 'Favoriten',
        'group_key' => 'card_edit',
        'group_name' => 'Bearbeiten',
        'order' => 18,
        'belongs_to' => ['Admin', 'Manager', 'Archivist', 'Viewer'],
        'visible' => 1
      ],
      [
        'name' => 'edit.address',
        'order' => 19,
        'belongs_to' => ['Admin'],
        'visible' => 0
      ],
      [
        'name' => 'edit.subscription',
        'order' => 20,
        'belongs_to' => ['Admin'],
        'visible' => 0
      ],
      [
        'name' => 'edit.clients',
        'order' => 21,
        'belongs_to' => ['Admin'],
        'visible' => 0
      ]
    ];
    
    // Get permissions for 'Admin'
    $adminPermissions = collect($permissions)->filter(function ($permission) {
      return in_array('Archivist', $permission['belongs_to']);
    });
    dd($adminPermissions);

  }

}