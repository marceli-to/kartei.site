<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Create
{
  public function execute(array $data): User
  {
    $user = User::withTrashed()->updateOrCreate(
      ['email' => $data['email']],
      [
        'firstname' => $data['firstname'],
        'name' => $data['name'],
        'deleted_at' => null
      ]
   );
  
  if ($user->wasRecentlyCreated) {
    $user->update([
      'password' => Hash::make($data['password']),
      'email_verified_at' => $data['email_verified_at'] ?? null,
      'company_id' => $data['company_id'] ?? null
    ]);

    // @todo: add further logic for newly created users
  }
  
  return $user;
  }
}
