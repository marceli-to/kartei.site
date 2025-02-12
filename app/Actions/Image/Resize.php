<?php
namespace App\Actions\Image;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;

class Resize
{
  private const MAX_WIDTH = 1200;
  private const MAX_HEIGHT = 1200;

  public function execute(string $sourcePath, string $destinationPath): Image
  {
    // Load the image from the source path
    $image = Image::load($sourcePath);

    // Get original dimensions
    $width = $image->getWidth();
    $height = $image->getHeight();

    // Determine which dimension is larger
    if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
      if ($width > $height) {
        $image->width(self::MAX_WIDTH)->fit(Fit::Contain);
      } 
      else {
        $image->height(self::MAX_HEIGHT)->fit(Fit::Contain);
      }
    }

    // Save the resized image to the destination path
    return $image->save($destinationPath);
  }
}