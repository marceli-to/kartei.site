<?php
namespace App\Actions\Archive;
use App\Models\Archive;

class Update
{
  public function execute(Archive $archive, array $data): Archive
  {
    $archive->update($data);
    return $archive;
  }
}
