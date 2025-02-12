<?php
namespace App\Actions\Image;
use Illuminate\Support\Facades\Storage;

class DeleteTemp
{
  public function execute($name)
  {

    $images = [
      $name,
      str_replace('_resized', '_original', $name),
    ];

    foreach ($images as $image)
    {
      try {
        $imagePath = 'temp/' . $image;
        if (Storage::disk('public')->exists($imagePath)) {
          Storage::disk('public')->delete($imagePath);
        }
      } 
      catch (\Exception $e) {
        \Log::error('Failed to delete image: ' . $e->getMessage());
        return false;
      }
    }
  }
}