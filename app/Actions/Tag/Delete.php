<?php
namespace App\Actions\Tag;
use App\Models\Tag;

class Delete
{
  public function execute(Tag $tag)
  { 
    return $tag->delete();
  }
}
