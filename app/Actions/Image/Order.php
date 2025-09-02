<?php
namespace App\Actions\Image;
use App\Models\Media;

class Order
{
  public function execute(array $positions)
  {
    foreach ($positions as $position) {
      if (isset($position['uuid']) && isset($position['position'])) {
        Media::where('uuid', $position['uuid'])
          ->update(['position' => $position['position']]);
      }
    }
    
    return true;
  }
}