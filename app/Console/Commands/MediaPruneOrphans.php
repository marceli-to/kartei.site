<?php
namespace App\Console\Commands;
use App\Models\Archive;
use App\Models\Media;
use App\Models\Record;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * One-off cleanup for images left behind before deletes removed them:
 * media rows whose record/archive is gone or soft-deleted, files in
 * archives/<uuid>/ without a media row, and folders of deleted archives.
 */
class MediaPruneOrphans extends Command
{
  protected $signature = 'media:prune-orphans {--force : Actually delete (default is a dry run)}';

  protected $description = 'Find (and with --force delete) orphaned image files and media rows';

  public function handle(): int
  {
    $disk = Storage::disk('public');
    $force = (bool) $this->option('force');

    // 1. Media rows without a live owner
    $liveRecords = Record::pluck('id')->flip();
    $archives = Archive::pluck('uuid', 'id');

    $orphanRows = Media::all()->filter(fn (Media $media) => match ($media->mediable_type) {
      Record::class => !$liveRecords->has($media->mediable_id),
      Archive::class => !$archives->has($media->mediable_id),
      default => true,
    });

    // 2. Files on disk that no remaining media row references
    $referenced = Media::whereNotIn('id', $orphanRows->pluck('id'))
      ->get(['original_name', 'resized_name'])
      ->flatMap(fn ($media) => [$media->original_name, $media->resized_name])
      ->filter()
      ->flip();

    $archiveUuids = $archives->flip();
    $orphanFiles = collect();
    $orphanDirectories = collect();

    foreach ($disk->directories('archives') as $directory) {
      if (!$archiveUuids->has(basename($directory))) {
        $orphanDirectories->push($directory);
        continue;
      }
      foreach ($disk->files($directory) as $file) {
        if (!$referenced->has(basename($file))) {
          $orphanFiles->push($file);
        }
      }
    }

    $this->line("Media rows without owner: {$orphanRows->count()}");
    $this->line("Unreferenced files: {$orphanFiles->count()}");
    $this->line("Folders of deleted archives: {$orphanDirectories->count()}");

    if (!$force) {
      foreach ($orphanDirectories as $directory) $this->line("  folder  $directory");
      foreach ($orphanFiles->take(50) as $file) $this->line("  file    $file");
      $this->warn('Dry run, nothing deleted. Run with --force to delete.');
      return self::SUCCESS;
    }

    Media::whereIn('id', $orphanRows->pluck('id'))->delete();
    $disk->delete($orphanFiles->all());
    $orphanDirectories->each(fn ($directory) => $disk->deleteDirectory($directory));

    $this->info('Deleted.');
    return self::SUCCESS;
  }
}
