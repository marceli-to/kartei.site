<?php
namespace App\Console\Commands;
use App\Actions\User\Delete as DeleteArchiveAction;
use App\Models\Archive;
use Illuminate\Console\Command;

class ArchiveDelete extends Command
{
  protected $signature = 'archive:delete';

  protected $description = 'Test the user delete action';

  public function handle()
  {
    $archives = Archive::all();
    $title = $this->choice('Which archive do you want to delete?', $archives->pluck('title')->toArray());
    $archive = Archive::where('title', $title)->first();
    (new DeleteArchiveAction())->execute($archive);
    $this->info('Archive deleted: ' . $archive->title);
  }
}