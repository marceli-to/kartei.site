<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Create
{
  public function execute(array $data): User
  {
    return User::create([
      'firstname' => $data['firstname'],
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
      'email_verified_at' => $data['email_verified_at'] ?? null,
      'company_id' => $data['company_id'] ?? null
    ]);
  }
}
