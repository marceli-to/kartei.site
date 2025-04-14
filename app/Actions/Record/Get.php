<?php
namespace App\Actions\Record;
use App\Models\Record;

class Get
{
  public function execute($data)
  {
    // Get the full record with all relations
    return Record::with('category', 'tags', 'media', 'fields', 'archive')->where('archive_id', $data['archive_id'])->get();
  }
}
