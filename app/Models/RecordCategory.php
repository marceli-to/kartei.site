<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class RecordCategory extends Model
{
  use HasUuid;
  
  protected $fillable = [
    'uuid', 
    'number', 
    'acronym', 
    'record_id', 
    'numeral_id', 
    'parent_id'
  ];

  public function record(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Record::class);
  }

  public function numeral(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Numeral::class);
  }

  public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(RecordCategory::class, 'parent_id');
  }

  public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(RecordCategory::class, 'parent_id');
  }
}