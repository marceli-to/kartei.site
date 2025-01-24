<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Tag extends Model
{
  use SoftDeletes;
  use HasUuid;
    
  protected $fillable = [
    'uuid', 
    'title'
  ];

  public function records(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
    return $this->belongsToMany(Record::class, 'record_tags');
  }
}