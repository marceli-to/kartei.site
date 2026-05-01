<?php
namespace App\Actions\Record;
use App\Models\Record;

class Delete
{
  public function execute(Record $record): Bool
  {
    return $record->delete();
  }
}
