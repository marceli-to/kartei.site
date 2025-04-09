<?php
namespace App\Actions\ArchiveTemplate;
use App\Models\Archive;

class Get
{
  public function execute(Archive $archive)
  {
    return $archive->template()->with('fields')->first();
  }
}
