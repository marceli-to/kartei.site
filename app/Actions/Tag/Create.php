<?php
namespace App\Actions\Tag;
use App\Models\Tag;

class Create
{
  public function execute($name, $archive_id)
  {
    return Tag::create([
      'name' => $name,
      'archive_id' => $archive_id
    ]);
  }
}