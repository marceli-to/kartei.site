<?php
namespace App\Actions\Archive;
use App\Models\Archive;

class Delete
{
  public function execute(Archive $archive): Bool
  {
    // @todo: delete associated records
    $archive->users()->detach();
    return $archive->delete();
  }
}
