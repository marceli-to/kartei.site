<?php
namespace App\Actions\UserPermission;
use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\Permission\Find as FindPermissionAction;
use App\Actions\Permission\Create as CreatePermissionAction;
use App\Models\User;

class Store
{
  public function execute(User $user, array $data)
  {
    // Loop over all archives
    foreach($data['archives'] as $archive)
    {
      // Find the archive by uuid
      $archive = (new FindArchiveAction())->execute($archive, true);
      
      // Loop over all permissions
      foreach($data['selectedPermissions'] as $permission)
      {
        // Find the permission
        $permission = (new FindPermissionAction())->execute($permission);
        
        // Create the permission
        $userPermission = (new CreatePermissionAction())->execute([
          'name' => $permission->name . '.' . $archive->id,
          'guard_name' => 'web',
          'publish' => false
        ]);
        
        // Assign the permission to the user
        $user->givePermissionTo($userPermission);
      }
    }
  }
}

