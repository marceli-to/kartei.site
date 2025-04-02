<?php
namespace App\Actions\ArchiveImage;
use App\Models\Media;
use App\Models\Archive;
use App\Actions\Image\Move as MoveImageAction;
use App\Actions\ArchiveImage\Delete as DeleteArchiveImageAction;

class Create
{
  public function execute($image, Archive $archive)
  {
    // If the image already exists (has a UUID), return the existing media
    if (isset($image['uuid'])) {
      return Media::where('uuid', $image['uuid'])->first();
    }

    // If not, delete the any old image first
    if ($archive->media) {
      (new DeleteArchiveImageAction())->execute($archive);
    }

    // Create and move new media
    $media = new Media([
      'name' => $image['name'],
      'original_name' => $image['original_name'],
      'resized_name' =>  $image['resized_name'],
      'resized_width' => $image['width'],
      'resized_height' => $image['height'],
      'aspect_ratio' => $image['aspect_ratio'],
      'mime_type' => $image['mime_type'],
      'size' => $image['size'],
    ]);

    (new MoveImageAction())->execute($image['original_name'], $archive->uuid);
    (new MoveImageAction())->execute($image['resized_name'], $archive->uuid);

    $media->mediable()->associate($archive);
    $media->save();

    return $media;
  }
  
}