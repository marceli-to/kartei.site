<?php
namespace App\Actions\Record;

use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\RecordImage\Create as CreateRecordImageAction;
use App\Models\Record;
use App\Models\RecordField;
use App\Models\Media;
use App\Models\Tag;
use App\Models\Category;

class Update
{
  public function execute(Record $record, array $data): Record
  {
    // Archive
    if (isset($data['archive_id'])) {
      $archive = (new FindArchiveAction())->execute($data['archive_id'], true);
      $data['archive_id'] = $archive->id;
    }

    // Tags
    if (isset($data['tags'])) {
      $tags = Tag::whereIn('uuid', $data['tags'])->get();
      $record->tags()->sync($tags->pluck('id')->toArray());
    }

    // Category
    if (isset($data['category'])) {
      $category = Category::where('uuid', $data['category'])->first();
      $data['category_id'] = $category->id;
    }

    // Update record base fields
    $record->update($data);

    // Fields (upsert)
    if (isset($data['fields'])) {
      foreach ($data['fields'] as $fieldData) {
        if (!empty($fieldData['content'])) {
          $record->fields()->updateOrCreate(
            ['uuid' => $fieldData['uuid']],
            [
              'placeholder' => $fieldData['placeholder'],
              'content' => $fieldData['content'],
            ]
          );
        } else {
          $record->fields()->where('uuid', $fieldData['uuid'])->delete();
        }
      }
    }

    // Media
    if (isset($data['media'])) {
      foreach ($data['media'] as $media) {
        if (!isset($media['uuid']) || !Media::where('uuid', $media['uuid'])->exists()) {
          (new CreateRecordImageAction())->execute($media, $record);
        }
      }
    }

    return $record;
  }
}
