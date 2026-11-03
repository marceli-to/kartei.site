<?php
namespace App\Actions\Image;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Archive;
use App\Models\Media;
use App\Models\Record;
use App\Models\User;

class Delete
{
  /**
   * Delete an uploaded image (resized + original) by its public url.
   *
   * Accepted locations:
   * - temp/<file>             unsaved uploads
   * - archives/<uuid>/<file>  saved images; the user must be allowed to edit
   *                           the archive (cover image) or its records
   */
  public function execute(string $url, User $user): bool
  {
    // Normalize the path: remove starting slashes and "storage/" if present
    $relativePath = preg_replace('#^storage/#', '', ltrim($url, '/'));
    $filename = basename($relativePath);
    $directory = dirname($relativePath);

    if (!$this->isValidFilename($filename)) {
      \Log::warning("Invalid image filename: $filename");
      return false;
    }

    $original = str_replace('_resized', '_original', $filename);

    if ($directory === 'temp') {
      $this->deleteFiles($directory, [$filename, $original]);
      return true;
    }

    if (!preg_match('#^archives/([0-9a-f-]{36})$#i', $directory, $matches)) {
      \Log::warning("Invalid image directory: $directory");
      return false;
    }

    $archive = Archive::where('uuid', $matches[1])->firstOrFail();

    // Media rows of this archive (its cover image or images of its records)
    $media = Media::where('resized_name', $filename)
      ->where(function ($query) use ($archive) {
        $query->where(function ($q) use ($archive) {
          $q->where('mediable_type', Archive::class)->where('mediable_id', $archive->id);
        })->orWhere(function ($q) use ($archive) {
          $q->where('mediable_type', Record::class)
            ->whereIn('mediable_id', Record::withTrashed()->where('archive_id', $archive->id)->select('id'));
        });
      })
      ->get();

    $ability = $media->contains('mediable_type', Archive::class) ? 'update' : 'updateRecord';
    Gate::forUser($user)->authorize($ability, $archive);

    $this->deleteFiles($directory, [$filename, $original]);
    Media::whereIn('id', $media->pluck('id'))->delete();

    return true;
  }

  private function isValidFilename(string $filename): bool
  {
    return str_contains($filename, '_resized')
      && preg_match('/^[A-Za-z0-9._-]+$/', $filename) === 1
      && !str_contains($filename, '..');
  }

  private function deleteFiles(string $directory, array $filenames): void
  {
    foreach ($filenames as $filename) {
      $path = $directory . '/' . $filename;
      if (Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
      }
      else {
        \Log::warning("File not found for deletion: $path");
      }
    }
  }
}
