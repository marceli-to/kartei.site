<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Archive;
use App\Actions\ArchiveUser\Attach as ArchiveUserAttachAction;

class ArchiveUserAttach extends Command
{
  protected $signature = 'archive:user:attach';

  protected $description = 'Test the archive user attach action';

  public function handle()
  {
    $users = User::all()->pluck('email', 'id')->toArray();
    $choice = $this->choice('Which user do you want to add to an archive?', $users);
    $userId = array_search($choice, $users);
    $user = User::find($userId);

    $archives = Archive::all()->pluck('title', 'id')->toArray();
    $choice = $this->choice('Which archive do you want to add this user to?', $archives);
    $archiveIds = array_search($choice, $archives);

    $archive_user = (new ArchiveUserAttachAction())->execute($user, [$archiveIds]);
    $this->info('Attached user ' . $user->email. ' to archive(s) ' . implode(', ', [$archiveIds]));
  }
}