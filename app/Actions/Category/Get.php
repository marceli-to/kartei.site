<?php
namespace App\Actions\Category;
use App\Models\Archive;
use App\Models\Category;

class Get
{
  public function execute(Archive $archive)
  {
    return $archive->categories()
      ->with('registers')
      ->get();
  }
}