<?php
namespace App\Policies;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Archive-scoped authorization.
 *
 * Super Admins are granted everything through Gate::before (AuthServiceProvider).
 * Archive admins (role "Admin", attached without a pivot role) may do everything
 * in their own archives. Other members need the matching per-archive permission,
 * stored as "<permission>.<archive id>".
 */
class ArchivePolicy
{
  use HandlesAuthorization;

  public function create(User $user): bool
  {
    return $user->hasRole('Admin');
  }

  public function view(User $user, Archive $archive): bool
  {
    return $user->isMemberOf($archive);
  }

  public function update(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['edit.archive', 'edit.basic_info']);
  }

  public function delete(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['delete.archive']);
  }

  public function manageStructure(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['edit.structure']);
  }

  public function manageTemplate(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['edit.template']);
  }

  public function manageTags(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['edit.tags']);
  }

  // Only the archive's own admin: the copy takes its members and their permissions along
  public function duplicate(User $user, Archive $archive): bool
  {
    return $user->isAdminOf($archive);
  }

  public function manageUsers(User $user, Archive $archive): bool
  {
    return $user->isAdminOf($archive);
  }

  public function createRecord(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['create.card']);
  }

  public function updateRecord(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['edit.card', 'manage.images', 'manage.description', 'manage.order']);
  }

  public function deleteRecord(User $user, Archive $archive): bool
  {
    return $this->allows($user, $archive, ['delete.card']);
  }

  private function allows(User $user, Archive $archive, array $permissions): bool
  {
    return $user->isAdminOf($archive) || $user->hasArchivePermission($archive, $permissions);
  }
}
