<?php
namespace App\Policies;
use App\Models\Address;
use App\Models\User;

/**
 * Users manage their own addresses. Super Admins are granted
 * everything through Gate::before (AuthServiceProvider).
 */
class AddressPolicy
{
  public function viewAny(User $user): bool
  {
    return false;
  }

  public function view(User $user, Address $address): bool
  {
    return $this->owns($user, $address);
  }

  /**
   * Addresses belong to account owners (role "Admin").
   */
  public function create(User $user): bool
  {
    return $user->hasRole('Admin');
  }

  public function update(User $user, Address $address): bool
  {
    return $this->owns($user, $address);
  }

  public function delete(User $user, Address $address): bool
  {
    return $this->owns($user, $address);
  }

  public function restore(User $user, Address $address): bool
  {
    return $this->owns($user, $address);
  }

  public function forceDelete(User $user, Address $address): bool
  {
    return $this->owns($user, $address);
  }

  private function owns(User $user, Address $address): bool
  {
    return $address->addressable_type === User::class
      && (int) $address->addressable_id === $user->id;
  }
}
