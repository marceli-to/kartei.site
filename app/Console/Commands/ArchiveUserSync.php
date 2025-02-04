<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Actions\ArchiveUser\Sync as ArchiveUserSyncAction;

class ArchiveUserSync extends Command
{
  protected $signature = 'archive:user:sync';

  protected $description = 'Test the archive user sync action';

  public function handle()
  {
    $users = User::all()->pluck('email', 'id')->toArray();
    $choice = $this->choice('Which user do you want to add to an archive?', $users);
    $userId = array_search($choice, $users);
    $user = User::find($userId);

    $archives = Archive::all()->pluck('title', 'id')->toArray();
    $choice = $this->choice('Which archive do you want to add this user to?', $archives);
    $archiveId = array_search($choice, $archives);
    $archive = Archive::find($archiveId);

    $archive_user = (new ArchiveUserSyncAction())->execute($user, $archive);
    $this->info('Synced user ' . $user->email. ' with archive ' . $archive->title);
  }
}