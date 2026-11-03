<?php
namespace App\Actions\Archive;
use App\Models\Archive;
use App\Models\Media;
use App\Models\Record;
use Illuminate\Support\Facades\Storage;

class Delete
{
  /**
   * Delete the archive with everything in it. The FK cascade removes
   * categories, records, tags and per-archive permissions; images
   * (polymorphic media rows and the files) are removed here.
   */
  public function execute(Archive $archive): bool
  {
    $recordIds = Record::withTrashed()->where('archive_id', $archive->id)->pluck('id');

    Media::where(function ($query) use ($archive, $recordIds) {
      $query->where(function ($q) use ($archive) {
        $q->where('mediable_type', Archive::class)->where('mediable_id', $archive->id);
      })->orWhere(function ($q) use ($recordIds) {
        $q->where('mediable_type', Record::class)->whereIn('mediable_id', $recordIds);
      });
    })->delete();

    Storage::disk('public')->deleteDirectory('archives/' . $archive->uuid);

    $archive->users()->detach();
    return $archive->delete();
  }
}
