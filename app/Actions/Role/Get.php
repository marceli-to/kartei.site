<?php
namespace App\Actions\Role;

use Spatie\Permission\Models\Role;
use App\Actions\Permission\Get as GetPermissions;
use Illuminate\Database\Eloquent\Collection;

class Get
{
  /**
   * Execute the action to get roles with optional constraints
   *
   * @param array $constraints Array of constraints to filter roles and their permissions
   * [
   *  'with_permissions' => bool,   // Include permissions with roles (default: false)
   *  'exclude' => array,           // Roles to exclude
   *  'permission_constraints' => [ // Constraints to pass to Permission\Get action
   *    'publish_only' => bool,   // Only return publishe permissions 
   *    'ordered' => bool,        // Apply order constraint
   *    'group_by_key' => bool,   // Group results by group_key
   *   ]
   * ]
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function execute(array $constraints = [])
  {
    $query = Role::query();

    // Eager load permissions if requested
    if (isset($constraints['with_permissions']) && $constraints['with_permissions']) {
      $query->with('permissions');
    }
    
    // Get the roles
    $roles = $query->get();
    
    // Apply permission constraints if needed
    if (isset($constraints['with_permissions']) && $constraints['with_permissions'] && 
        isset($constraints['permission_constraints']) && is_array($constraints['permission_constraints'])) {
      
      // Get Permission action
      $permissionAction = new GetPermissions();
      
      $roles = $roles->map(function ($role) use ($permissionAction, $constraints) {

        // Apply constraints to the role's permissions using the Permission\Get action
        $permissionConstraints = array_merge(
          ['role' => $role], // This tells the Permission\Get action to get permissions for this role
          $constraints['permission_constraints']
        );
        
        // Get filtered permissions using Permission\Get action
        $permissions = $permissionAction->execute($permissionConstraints);
        
        // Replace the permissions collection with the filtered one
        $role->setRelation('permissions', $permissions);
        
        return $role;
      });
    }
    
    // Exclude roles if requested
    if (isset($constraints['exclude']) && is_array($constraints['exclude'])) {
      $roles = $roles->filter(function ($role) use ($constraints) {
        return !in_array($role->name, $constraints['exclude']);
      });
    }
    
    return $roles;
  }
}