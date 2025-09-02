<?php
namespace App\Actions\Favorite;

class Get
{
  public function execute()
  {
    return auth()->user()->getFavorites();
  }
}