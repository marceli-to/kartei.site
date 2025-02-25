<?php
namespace App\Actions\User;
use App\Models\User;

class Update
{
  public function execute(array $data, User $user): User
  {
    // Set the data
    $user->fill($data);
    
    // If email changed, send verification notification
    // and set email_verified_at to null
    if ($user->isDirty('email'))
    {
      $user->email_verified_at = null;
      $user->save();
      $user->sendEmailVerificationNotification();
      return $user;
    }

    // Save user
    $user->save();

    return $user;
  }
}
