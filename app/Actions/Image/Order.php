<?php
namespace App\Actions\Image;
use Illuminate\Support\Facades\Gate;
use App\Models\Archive;
use App\Models\Media;
use App\Models\Record;
use App\Models\User;

class Order
{
  /**
   * Update the position of record images. Every image must belong to a
   * record the user is allowed to edit.
   */
  public function execute(array $positions, User $user): bool
  {
    $positions = collect($positions)
      ->filter(fn ($position) => isset($position['uuid'], $position['position']));

    $media = Media::whereIn('uuid', $positions->pluck('uuid'))->get()->keyBy('uuid');

    $archiveIds = $media->map(function (Media $item) {
      return match ($item->mediable_type) {
        Record::class => Record::withTrashed()->whereKey($item->mediable_id)->value('archive_id'),
        Archive::class => $item->mediable_id,
        default => null,
      };
    })->unique();

    foreach ($archiveIds as $archiveId) {
      $archive = Archive::findOrFail($archiveId);
      Gate::forUser($user)->authorize('updateRecord', $archive);
    }

    foreach ($positions as $position) {
      if ($media->has($position['uuid'])) {
        $media[$position['uuid']]->update(['position' => (int) $position['position']]);
      }
    }

    return true;
  }
}
