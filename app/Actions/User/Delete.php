<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Delete
{
  public function execute(User $user): Bool
  {

    // remove roles
    $user->roles()->detach();

    // remove permissions
    $user->permissions()->detach();

    return $user->delete();
  }
}
