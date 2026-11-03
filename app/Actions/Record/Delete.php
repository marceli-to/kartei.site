<?php
namespace App\Actions\Record;
use App\Actions\Media\Purge as PurgeMediaAction;
use App\Models\Record;

class Delete
{
  /**
   * Soft-delete the record. Its images are removed for good, since
   * deleted records can't be restored.
   */
  public function execute(Record $record): bool
  {
    $record->loadMissing('archive', 'media');
    (new PurgeMediaAction())->execute($record->media, $record->archive->uuid);
    return $record->delete();
  }
}
