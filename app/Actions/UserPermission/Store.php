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
    // Find the archive by uuid
    $archive = (new FindArchiveAction())->execute($data['archive'], true);
    
    if (!$archive) {
      return;
    }
    
    // First, remove any existing permissions for this archive
    $this->removeExistingPermissions($user, $archive);
    
    // Loop over all selected permissions
    foreach($data['selectedPermissions'] as $permissionId)
    {
      // Find the permission
      $permission = (new FindPermissionAction())->execute($permissionId);
      
      if (!$permission) {
        continue;
      }
      
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
  
  /**
   * Remove existing permissions for the given archive
   */
  private function removeExistingPermissions(User $user, $archive)
  {
    // Get all permissions for the user
    $userPermissions = $user->getAllPermissions();
    
    foreach ($userPermissions as $permission) {
      // Check if this permission is for the current archive
      if (str_ends_with($permission->name, '.' . $archive->id)) {
        // Revoke this permission
        $user->revokePermissionTo($permission);
      }
    }
  }
}