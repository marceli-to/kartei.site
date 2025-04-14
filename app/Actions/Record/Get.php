<?php
namespace App\Actions\Record;
use App\Models\Record;

class Get
{
  public function execute($data)
  {
    $records = Record::with('category', 'tags', 'media', 'fields', 'archive')
      ->where('archive_id', $data['archive_id'])
      ->get();

    // Sort records by category global order
    $sortedRecords = $records->sortBy('category.global_order');
    
    return $sortedRecords;
  }
}
