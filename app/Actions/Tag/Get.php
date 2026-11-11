<?php
namespace App\Actions\Tag;
use App\Models\Archive;

class Get
{
  public function execute(Archive $archive)
  {
    return $archive->tags()->get();
  }
}