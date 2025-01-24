<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Numeral extends Model
{
  use SoftDeletes;
  
  protected $fillable = [
    'uuid',
    'title'
  ];

  public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(RecordCategory::class);
  }
}