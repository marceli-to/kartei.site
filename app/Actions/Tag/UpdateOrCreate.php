<?php
namespace App\Actions\Tag;
use App\Models\Tag;
use App\Models\Archive;

class UpdateOrCreate
{
  public function execute($tags, Archive $archive): bool
  {
    // The list order is the tag order (drag + drop in the settings)
    foreach (array_values($tags ?? []) as $index => $tag) {
      Tag::updateOrCreate(
        [
          'uuid' => $tag['uuid'],
          'archive_id' => $archive->id,
        ],
        [
          'name' => $tag['name'],
          'order' => $index,
        ]
      );
    }
    return true;
  }
}
