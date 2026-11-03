<?php
namespace App\Actions\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Create
{
  /**
   * Create a user account, or return the existing account for this email.
   *
   * Existing active accounts are never modified: their name, password and
   * creator stay untouched. A soft-deleted account is restored.
   */
  public function execute(array $data): User
  {
    $existingUser = User::withTrashed()
      ->where('email', $data['email'])
      ->first();

    if ($existingUser && !$existingUser->trashed()) {
      return $existingUser;
    }

    if ($existingUser) {
      $existingUser->restore();
      $existingUser->fill([
        'firstname' => $data['firstname'],
        'name' => $data['name'],
      ]);
      $existingUser->created_by = $data['created_by'] ?? null;
      $existingUser->save();
      return $existingUser;
    }

    $user = new User([
      'firstname' => $data['firstname'],
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
      'company_id' => $data['company_id'] ?? null,
      // Only trusted callers (console) pass this; HTTP input never reaches it
      'email_verified_at' => $data['email_verified_at'] ?? null,
    ]);
    $user->created_by = $data['created_by'] ?? null;
    $user->save();

    return $user;
  }
}
