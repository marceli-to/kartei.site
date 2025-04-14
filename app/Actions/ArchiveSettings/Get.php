<?php
namespace App\Actions\ArchiveSettings;
use App\Models\Archive;

class Get
{
  public function execute(Archive $archive)
  {
    return $archive->settings;
  }
}
