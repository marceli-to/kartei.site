<?php
namespace App\Actions\RecordImage;
use App\Models\Media;
use App\Models\Record;
use App\Actions\Image\Move as MoveImageAction;
use App\Actions\RecordImage\Delete as DeleteRecordImageAction;

class Create
{
  public function execute($image, Record $record)
  {
    // If the image already exists (has a UUID), return the existing media
    if (isset($image['uuid'])) {
      return Media::where('uuid', $image['uuid'])->first();
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

    (new MoveImageAction())->execute($image['original_name'], $record->archive->uuid);
    (new MoveImageAction())->execute($image['resized_name'], $record->archive->uuid);

    $media->mediable()->associate($record);
    $media->save();

    return $media;
  }
  
}