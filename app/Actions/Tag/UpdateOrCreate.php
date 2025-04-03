<?php
namespace App\Actions\Tag;
use App\Models\Tag;
use App\Models\Archive;

class UpdateOrCreate
{
  public function execute($tags, Archive $archive): bool
  {
    foreach ($tags as $tag) {
      Tag::updateOrCreate(
        [
          'name' => $tag['name'],
          'archive_id' => $archive->id,
        ],
        [
          'name' => $tag['name'],
        ]
      );
    }
    return true;
  }
}
