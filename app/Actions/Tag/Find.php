<?php
namespace App\Actions\Tag;
use App\Models\Tag;

class Find
{
  public function execute(Tag $tag)
  {
    return $tag;
  }
}