<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdatePassword
{
  public function execute(array $data, User $user): User
  {
    $user->password = Hash::make($data['new_password']);
    $user->save();
    return $user;
  }
}
