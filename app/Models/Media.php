<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Media extends Model
{
  use HasUuid;

  protected $fillable = [
    'uuid',
    'name',
    'original_name',
    'resized_name',
    'resized_width',
    'resized_height',
    'aspect_ratio',
    'mime_type',
    'size',
    'position'
  ];

  protected static function boot()
  {
    parent::boot();
    
    static::addGlobalScope('ordered', function ($query) {
      $query->orderBy('position');
    });
  }

  public function mediable()
  {
    return $this->morphTo();
  }
}