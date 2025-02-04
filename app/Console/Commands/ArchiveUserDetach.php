<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Actions\ArchiveUser\Detach as ArchiveUserDetachAction;

class ArchiveUserDetach extends Command
{
  protected $signature = 'archive:user:detach';

  protected $description = 'Test the archive user detach action';

  public function handle()
  {
    $users = User::all()->pluck('email', 'id')->toArray();
    $choice = $this->choice('Which user do you want to remove from an archive?', $users);
    $userId = array_search($choice, $users);
    $user = User::find($userId);

    $archives = Archive::all()->pluck('title', 'id')->toArray();
    $choice = $this->choice('Which archive do you want to remove this user from?', $archives);
    $archiveId = array_search($choice, $archives);
    $archive = Archive::find($archiveId);

    $archive_user = (new ArchiveUserDetachAction())->execute($user, $archive);
    $this->info('Detached user ' . $user->email. ' from archive ' . $archive->title);
  }
}