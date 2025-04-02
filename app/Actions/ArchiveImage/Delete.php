<?php
namespace App\Actions\ArchiveImage;
use App\Models\Archive;
use Illuminate\Support\Facades\Storage;

class Delete
{
  private const ARCHIVE_DIRECTORY = 'archives/';

  public function execute(Archive $archive): bool
  {
    $images = [
      $archive->media->original_name,
      $archive->media->resized_name
    ];

    // Images are located in the public storage/{archive uuid}/
    try {
      foreach ($images as $image) {
        $imagePath = self::ARCHIVE_DIRECTORY . $archive->uuid . '/' . $image;
        if (Storage::disk('public')->exists($imagePath)) {
          Storage::disk('public')->delete($imagePath);
        }
      }
      $archive->media()->forceDelete();
      return true;
    } 
    catch (\Exception $e) {
      \Log::error('Failed to delete image: ' . $e->getMessage());
      return false;
    }
  }
}