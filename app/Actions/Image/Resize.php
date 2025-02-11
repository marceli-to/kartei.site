<?php
namespace App\Actions\Image;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;

class Resize
{
  private const MAX_WIDTH = 1200;
  private const MAX_HEIGHT = 1200;

  public function execute(string $imagePath)
  {
    // Load the image
    $image = Image::load($imagePath);

    // Get original dimensions
    $width = $image->getWidth();
    $height = $image->getHeight();

    // Determine which dimension is larger
    if ($width > $height) {
      $image->width(self::MAX_WIDTH)->fit(Fit::Contain);
    } 
    else {
      $image->height(self::MAX_HEIGHT)->fit(Fit::Contain);
    }

    // Save the resized image
    $image->save($imagePath);

    return [
      'path' => $imagePath,
      'width' => $image->getWidth(),
      'height' => $image->getHeight()
    ];
  }
}