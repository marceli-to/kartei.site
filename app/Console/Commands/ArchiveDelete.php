<?php
namespace App\Console\Commands;
use App\Actions\Archive\Delete as DeleteArchiveAction;
use App\Models\Archive;
use Illuminate\Console\Command;

class ArchiveDelete extends Command
{
  protected $signature = 'archive:delete';

  protected $description = 'Test the user delete action';

  public function handle()
  {
    $archives = Archive::all()->pluck('title', 'id')->toArray();
    $choice = $this->choice('Which archive do you want to delete?', $archives);
    $archiveId = array_search($choice, $archives);
    $archive = Archive::find($archiveId);
    (new DeleteArchiveAction())->execute($archive);
    $this->info('Archive deleted: ' . $archive->title);
  }
}