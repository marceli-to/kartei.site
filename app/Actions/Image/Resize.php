<?php
namespace App\Actions\Image;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;

class Resize
{
  private const MAX_WIDTH = 1200;
  private const MAX_HEIGHT = 1200;

  public function execute(string $sourcePath, string $destinationPath): array
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

    $image->save($destinationPath);

    $newWidth = $image->getWidth();
    $newHeight = $image->getHeight();
    
    return [
      'width' => $newWidth,
      'height' => $newHeight,
      'aspect_ratio' => $this->determineAspectRatio($newWidth, $newHeight),
    ];
  }

  protected function determineAspectRatio(int $width, int $height): string
  {
    if ($width > $height) {
      return 'landscape';
    }
    elseif ($width < $height) {
      return 'portrait';
    }
    return 'square';
  }
}