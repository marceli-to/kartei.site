<?php
namespace App\Actions\Favorite;
use App\Models\Favorite;
use App\Models\Record;

class Toggle
{
  public function execute($id)
  {
    $favorite = Favorite::firstOrNew([
      'user_id' => auth()->user()->id,
      'record_id' => $id
    ]);
    $favorite->exists ? $favorite->delete() : $favorite->save();
  }
}