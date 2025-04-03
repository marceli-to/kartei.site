<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Numeral extends Model
{
  use HasUuid;
    
  protected $fillable = [
    'uuid',
    'title'
  ];

  public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(RecordCategory::class);
  }
}