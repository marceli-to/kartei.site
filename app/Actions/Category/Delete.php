<?php
namespace App\Actions\Category;
use App\Actions\Media\Purge as PurgeMediaAction;
use App\Models\Category;
use App\Models\Media;
use App\Models\Record;

class Delete
{
  public function execute(Category $category): bool
  {
    // The FK cascade removes registers and records (including soft-deleted
    // ones), but not their images: the media relation is polymorphic
    $categoryIds = $category->children()->pluck('id')->push($category->id);
    $recordIds = Record::withTrashed()->whereIn('category_id', $categoryIds)->pluck('id');

    $media = Media::where('mediable_type', Record::class)
      ->whereIn('mediable_id', $recordIds)
      ->get();

    (new PurgeMediaAction())->execute($media, $category->archive->uuid);

    return $category->delete();
  }
}
