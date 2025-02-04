<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Delete
{
  public function execute(User $user): Bool
  {
    return $user->delete();
  }
}
