<?php
namespace App\Actions\Role;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class Assign
{
  public function execute(User $user, Role $role): array
  {
    return $user->roles()->sync([$role->id]);
  }
}
