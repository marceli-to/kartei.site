<?php
namespace App\Actions\Tag;
use App\Models\Tag;
use App\Models\Archive;

class Get
{
  public function execute(Archive $archive)
  {
    return Tag::where('archive_id', $archive->id)->get();
  }
}