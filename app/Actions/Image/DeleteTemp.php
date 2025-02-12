<?php
namespace App\Actions\Image;
use Illuminate\Support\Facades\Storage;

class DeleteTemp
{
  private const TEMP_DIRECTORY = 'temp/';

  public function execute(string $name): bool
  {
    $images = [
      $name,
      str_replace('_resized', '_original', $name)
    ];

    try {
      foreach ($images as $image) {
        $imagePath = self::TEMP_DIRECTORY . $image;
        if (Storage::disk('public')->exists($imagePath)) {
          Storage::disk('public')->delete($imagePath);
        }
      }
      return true;
    } 
    catch (\Exception $e) {
      \Log::error('Failed to delete image: ' . $e->getMessage());
      return false;
    }
  }
}