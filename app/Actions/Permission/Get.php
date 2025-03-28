<?php
namespace App\Actions\Permission;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Archive;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Get
{
	/**
	 * Execute the action to get permissions with optional constraints
	 *
	 * @param array $constraints Array of constraints to filter permissions
	 *                          [
	 *                              'role' => Role|string|int,  // Filter by role
	 *                              'user' => User|int,         // Filter by user
	 *                              'publish_only' => bool,     // Only return publish permissions (default: false)
   *                              'hidden' => bool,          // Only return hidden permissions (default: false)
	 *                              'ordered' => bool,          // Apply order constraint (default: false)
	 *                              'group_by_key' => bool,     // Group results by group_key (default: false)
   *                              'group_by_archive_id' => bool, // Group results by archive_id (default: false)
	 *                          ]
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
	 */
	public function execute(array $constraints = [])
	{
		// Get permissions based on role/user constraints
		if (isset($constraints['role'])) {
			$permissions = $this->getPermissionsByRole($constraints['role']);
		} elseif (isset($constraints['user'])) {
			$permissions = $this->getPermissionsByUser($constraints['user']);
		} else {
			// Start with a base query if no role/user specified
			$query = Permission::query();
			$permissions = $query->get();
		}
		
		// Apply publish_only filter if requested
		if (isset($constraints['publish_only']) && $constraints['publish_only']) {
			$permissions = $permissions->where('publish', true);
		}

		// Apply hidden filter if requested
		if (isset($constraints['hidden']) && $constraints['hidden']) {
			$permissions = $permissions->where('publish', false);
		}
		
		// Apply ordering if requested
		if (isset($constraints['ordered']) && $constraints['ordered']) {
			$permissions = $permissions->sortBy('order');
		}
		
		// Group results by group_key if requested
		if (isset($constraints['group_by_key']) && $constraints['group_by_key']) {
			return $permissions->groupBy('group_key');
		}
		
		// Group results by archive_id if requested
		if (isset($constraints['group_by_archive_id']) && $constraints['group_by_archive_id']) {
      $permissions = $this->getPermissionsByArchive($permissions);
		}
		
		return $permissions;
	}

	/**
	 * Get permissions by role
	 *
	 * @param Role|string|int $role The role instance, role name or role ID
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	private function getPermissionsByRole($role): Collection
	{
		if (!$role instanceof Role) {
			if (is_numeric($role)) {
				$role = Role::findById($role);
			} else {
				$role = Role::findByName($role);
			}
		}

		return $role->permissions;
	}

	/**
	 * Get permissions by user
	 *
	 * @param User|int $user The user instance or user ID
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	private function getPermissionsByUser($user): Collection
	{
		if (!$user instanceof User) {
			$user = User::findOrFail($user);
		}

		return $user->getAllPermissions();
	}

  /**
   * Get permissions by archive
   *
   * @param \Illuminate\Database\Eloquent\Collection $permissions The collection of permissions
   * @return \Illuminate\Support\Collection
   */
  // private function getPermissionsByArchive(Collection $permissions): SupportCollection
  // {
  //   $uuidCache = [];

  //   // Step 1: Extract base names and archive IDs
  //   $permissions->each(function ($permission) {
  //     $parts = explode('.', $permission->name);
  //     $permission->archive_id = array_pop($parts);
  //     $permission->base_name = implode('.', $parts);
  //   });

  //   // Step 2: Preload all base permissions in a single query
  //   $baseNames = $permissions->pluck('base_name')->unique()->all();
  //   $basePermissions = Permission::whereIn('name', $baseNames)->get()->keyBy('name');

  //   // Step 3: Group by archive UUID
  //   $grouped = $permissions->groupBy(function ($permission) use (&$uuidCache) {
  //     $archiveId = $permission->archive_id;

  //     if (!isset($uuidCache[$archiveId])) {
  //       $archive = Archive::find($archiveId);
  //       $uuidCache[$archiveId] = $archive?->uuid ?? 'unknown';
  //     }
  //     return $uuidCache[$archiveId];
  //   });

  //   // Step 4: Map to clean structure
  //   return $grouped->map(function ($group) use ($basePermissions) {
  //     return $group->map(function ($permission) use ($basePermissions) {
  //       return [
  //         'id' => $basePermissions[$permission->base_name]->id ?? null,
  //         'name' => $permission->base_name,
  //       ];
  //     })->filter(fn ($perm) => !is_null($perm['id']));
  //   });
  // }

  private function getPermissionsByArchive(Collection $permissions): SupportCollection
  {
      $uuidCache = [];
  
      // Add archive_id and base_name to each permission
      $permissions->each(function ($permission) {
          $parts = explode('.', $permission->name);
          $permission->archive_id = array_pop($parts);
          $permission->base_name = implode('.', $parts);
      });
  
      $baseNames = $permissions->pluck('base_name')->unique()->all();
      $basePermissions = Permission::whereIn('name', $baseNames)->get()->keyBy('name');
  
      // Get the user ID from the permission object (they all come from same user context)
      $userId = optional($permissions->first())->pivot->model_id ?? null;
      $user = $userId ? User::find($userId) : null;
  
      // Group by archive UUID
      $grouped = $permissions->groupBy(function ($permission) use (&$uuidCache) {
          $archiveId = $permission->archive_id;
  
          if (!isset($uuidCache[$archiveId])) {
              $archive = Archive::find($archiveId);
              $uuidCache[$archiveId] = $archive?->uuid ?? 'unknown';
          }
  
          return $uuidCache[$archiveId];
      });
  
      // Final structure
      return $grouped->map(function ($group) use ($basePermissions, $user) {
          $archiveId = $group->first()->archive_id;
  
          // Get user's role for this archive
          $role = null;
          if ($user) {
              $roleId = $user->archives()->where('archive_id', $archiveId)->first()?->pivot->role_id;
              $roleModel = $roleId ? \Spatie\Permission\Models\Role::find($roleId) : null;
              $role = $roleModel ? [
                  'id' => $roleModel->id,
                  'name' => $roleModel->name,
              ] : null;
          }
  
          // Build permission list
          $permissions = $group->map(function ($permission) use ($basePermissions) {
              return [
                  'id' => $basePermissions[$permission->base_name]->id ?? null,
                  'name' => $permission->base_name,
              ];
          })->filter(fn ($perm) => !is_null($perm['id']));
  
          return [
              'role' => $role,
              'permissions' => $permissions->values(),
          ];
      });
  }
  
}