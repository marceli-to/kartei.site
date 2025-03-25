<?php
namespace App\Actions\UserInvite;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\UserInvitation;

class Invite
{
  public function execute(User $user, array $archives): void
  {
    Notification::route('mail', $user->email)->notify(new UserInvitation(
      [
        'email' => $user->email,
        'name' => $user->name,
        'firstname' => $user->firstname,
        'archives' => $archives
      ]
    ));
    return;
  }
}
