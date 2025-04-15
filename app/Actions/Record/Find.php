<?php
namespace App\Actions\Record;
use App\Models\Record;

class Find
{
  public function execute(Record $record)
  {
    return Record::with('category', 'tags', 'media', 'fields', 'archive')->find($record->id);
  }
}
