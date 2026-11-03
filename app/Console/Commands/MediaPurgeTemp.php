<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MediaPurgeTemp extends Command
{
  protected $signature = 'media:purge-temp {--hours=24 : Delete uploads older than this}';

  protected $description = 'Delete abandoned uploads from storage/app/public/temp';

  public function handle(): int
  {
    $disk = Storage::disk('public');
    $threshold = now()->subHours((int) $this->option('hours'))->getTimestamp();

    $old = collect($disk->files('temp'))
      ->filter(fn ($path) => $disk->lastModified($path) < $threshold)
      ->values();

    $disk->delete($old->all());

    $this->info("Deleted {$old->count()} file(s) from temp/.");
    return self::SUCCESS;
  }
}
