<?php
namespace App\Policies;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization for archive admins managing other users.
 *
 * Super Admins are granted everything through Gate::before (AuthServiceProvider).
 * Account admins (role "Admin" / "Super Admin") can never be managed by others.
 */
class UserPolicy
{
  use HandlesAuthorization;

  /**
   * Create new user accounts (to add them to own archives).
   */
  public function create(User $actor): bool
  {
    return $actor->hasRole('Admin');
  }

  /**
   * View a user and their permissions: the target was created by the actor
   * or is a member of an archive the actor administers.
   */
  public function view(User $actor, User $target): bool
  {
    if (!$this->isManageable($actor, $target)) {
      return false;
    }
    return $this->isCreator($actor, $target) || $this->sharesAdministeredArchive($actor, $target);
  }

  /**
   * Edit the target's profile (name, email) or delete the account.
   * Only the admin who created the account may do that.
   */
  public function update(User $actor, User $target): bool
  {
    return $this->isManageable($actor, $target) && $this->isCreator($actor, $target);
  }

  public function delete(User $actor, User $target): bool
  {
    return $this->view($actor, $target);
  }

  /**
   * Grant the target access to an archive the actor administers.
   */
  public function assignToArchive(User $actor, User $target, Archive $archive): bool
  {
    return $this->isManageable($actor, $target) && $actor->isAdminOf($archive);
  }

  private function isManageable(User $actor, User $target): bool
  {
    return $actor->hasRole('Admin')
      && $actor->id !== $target->id
      && !$target->isAccountAdmin();
  }

  private function isCreator(User $actor, User $target): bool
  {
    return $target->created_by !== null && (int) $target->created_by === $actor->id;
  }

  private function sharesAdministeredArchive(User $actor, User $target): bool
  {
    return $target->archives()
      ->get()
      ->contains(fn (Archive $archive) => $actor->isAdminOf($archive));
  }
}
