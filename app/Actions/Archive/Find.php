<?php
namespace App\Actions\Archive;
use App\Models\Archive;

class Find
{
  public function execute($id, $isUuid = false): Archive
  {
    if ($isUuid) {
      return Archive::where('uuid', $id)->first();
    }
    return Archive::find($id);
  }
}
