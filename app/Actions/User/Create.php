<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Create
{
  public function execute(array $data): User
  {
    $existingUser = User::withTrashed()
      ->where('email', $data['email'])
      ->first();

    $user = User::withTrashed()->updateOrCreate(
      ['email' => $data['email']],
      [
        'firstname' => $data['firstname'],
        'name' => $data['name'],
        'password' => $existingUser?->password ?? Hash::make($data['password']),
        'deleted_at' => null
      ]
    );
  
    if ($user->wasRecentlyCreated) {
      $user->update([
        'email_verified_at' => $data['email_verified_at'] ?? null, 
        'company_id' => $data['company_id'] ?? null
      ]);

      // @todo: additional logic for newly registered users
    }
  
    return $user;
  }
}
