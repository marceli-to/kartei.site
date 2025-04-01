<?php
namespace App\Actions\ArchiveImage;
use App\Models\Media;
use App\Actions\Image\Move;

class Create
{
  public function execute($images, $archive)
  {
    foreach ($images as $image)
    {
      $media = new Media([
        'name' => $image['original']['original_name'],
        'original_name' => $image['original']['name'],
        'resized_name' =>  $image['resized']['name'],
        'resized_width' => $image['resized']['width'],
        'resized_height' => $image['resized']['height'],
        'aspect_ratio' => $image['resized']['aspect_ratio'],
        'mime_type' => $image['original']['mime_type'],
        'size' => $image['original']['size'],
      ]);

      (new Move())->execute($image['original']['name'], $archive->uuid);
      (new Move())->execute($image['resized']['name'], $archive->uuid);
    }

    $media->mediable()->associate($archive);
    $media->save();

    return $media;
  }
}