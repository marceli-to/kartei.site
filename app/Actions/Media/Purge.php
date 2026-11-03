<?php
namespace App\Actions\Media;
use App\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Purge
{
  /**
   * Delete media rows together with their files (original + resized),
   * which live in archives/<archive uuid>/ on the public disk.
   */
  public function execute(Collection $media, string $archiveUuid): int
  {
    $disk = Storage::disk('public');
    $directory = 'archives/' . $archiveUuid;

    $files = $media
      ->flatMap(fn (Media $item) => [$item->original_name, $item->resized_name])
      ->filter(fn ($name) => $name && $name === basename($name))
      ->map(fn ($name) => $directory . '/' . $name)
      ->unique()
      ->values()
      ->all();

    if ($files) {
      $disk->delete($files);
    }

    return Media::whereIn('id', $media->pluck('id'))->delete();
  }
}
