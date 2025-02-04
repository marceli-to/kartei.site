<?php
namespace App\Actions\User;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AssignRole
{
  public function execute(User $user, Role $role): array
  {
    return $user->roles()->sync([$role->id]);
  }
}
