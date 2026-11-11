<?php
namespace App\Actions\UserInvite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Models\Archive;
use App\Models\User;
use App\Notifications\UserInvitation;

class Invite
{
  public function execute(User $user, Archive $archive): void
  {
    // An account that still has the welcome password has never set its own,
    // so it gets the credentials. Anyone else is only told about the new archive.
    $existing = !Hash::check(config('user.welcome_password'), $user->password);

    Notification::route('mail', $user->email)->notify(new UserInvitation(
      [
        'email' => $user->email,
        'name' => $user->name,
        'firstname' => $user->firstname,
        'archive' => $archive->uuid,
        'archive_name' => $archive->name,
        'existing' => $existing,
      ]
    ));
  }
}
