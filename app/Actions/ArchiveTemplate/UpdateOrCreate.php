<?php
namespace App\Actions\ArchiveTemplate;
use App\Models\Archive;
use App\Models\ArchiveTemplate;

class UpdateOrCreate
{
  public function execute(array $template, Archive $archive)
  {
    return $archive->template()->updateOrCreate(
      [], 
      ['image' => $template['image']]
    );

  }

}
