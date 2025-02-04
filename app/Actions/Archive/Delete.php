<?php
namespace App\Actions\User;
use App\Models\User;

class Delete
{
  public function execute(User $user): Bool
  {
    return $user->delete();
  }
}
