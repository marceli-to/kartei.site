<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Delete
{
  public function execute(User $user): Bool
  {
    // todo: remove roles and permissions
    return $user->delete();
  }
}
