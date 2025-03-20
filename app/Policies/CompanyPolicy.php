<?php
namespace App\Policies;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can view any companies.
   */
  public function viewAny(User $user): bool
  {
    return true; // All authenticated users can view their companies
  }

  /**
   * Determine whether the user can view the company.
   */
  public function view(User $user, Company $company): bool
  {
    return $user->id === $company->user_id;
  }

  /**
   * Determine whether the user can create companies.
   */
  public function create(User $user): bool
  {
    return true; // All authenticated users can create companies
  }

  /**
   * Determine whether the user can update the company.
   */
  public function update(User $user, Company $company): bool
  {
    return $user->id === $company->user_id;
  }

  /**
   * Determine whether the user can delete the company.
   */
  public function delete(User $user, Company $company): bool
  {
    return $user->id === $company->user_id;
  }
}