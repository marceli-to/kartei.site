<?php
namespace App\Actions\Record;
use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\RecordImage\Create as CreateRecordImageAction;
use App\Models\Record;
use App\Models\RecordField;
use App\Models\Tag;
use App\Models\Category;

class Create
{
  public function execute(array $data): Record
  {
    // Find archive by uuid
    $archive = (new FindArchiveAction())->execute($data['archive_id'], true);
    $data['archive_id'] = $archive->id;

    // Find tags by uuid
    if (isset($data['tags'])) {
      $tags = Tag::whereIn('uuid', $data['tags'])->get();
      $data['tags'] = $tags;
    }

    // Find category by uuid
    if (isset($data['category'])) {
      $category = Category::where('uuid', $data['category'])->first();
      $data['category_id'] = $category->id;
    }

    // Create record
    $record = Record::create($data);

    // Add tags to record_tags
    if (isset($data['tags'])) {
      $record->tags()->attach($tags->pluck('id')->toArray());
    }

    // Add fields to record_fields
    if (isset($data['fields'])) {
      $record->fields()->createMany(
        collect($data['fields'])
        ->filter(fn($field) => !empty($field['content'])) // Only keep fields with non-empty content
        ->map(fn($field) => [
            'uuid' => $field['uuid'],
            'placeholder' => $field['placeholder'],
            'content' => $field['content'],
        ])
        ->all()
      );
    }

    if (isset($data['media'])) {
      foreach($data['media'] as $media) {
        (new CreateRecordImageAction())->execute($media, $record);
      }
    }

    return $record;
  }
}
