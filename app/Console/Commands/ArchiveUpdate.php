<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Archive\Update as UpdateArchiveAction;
use App\Models\Archive;

class ArchiveUpdate extends Command
{
  protected $signature = 'archive:update';

  protected $description = 'Test the archive update action';

  public function handle()
  {
    $archives = Archive::all()->pluck('name', 'id')->toArray();
    $choice = $this->choice('Which archive do you want to update?', $archives);
    $archiveId = array_search($choice, $archives);
    $archive = Archive::find($archiveId);
    
    $data = [
      'name' => $this->ask('Enter new name') ?? $archive->name,
      'acronym' => $this->ask('Enter new acronym') ?? $archive->acronym,
      'media_id' => $this->ask('Enter new media id') ?? $archive->media_id,
      'company_id' => $this->ask('Enter new company id') ?? $archive->company_id
    ];

    $archive = (new UpdateArchiveAction())->execute($data, $archive);
    $this->info('Archive updated: ' . $archive->name);
  }
}