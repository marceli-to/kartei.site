<?php
namespace App\Actions\Role;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Assign
{
  public function execute(User $user, $roleId): array
  {
    return $user->roles()->syncWithoutDetaching([$roleId]);
  }
}
