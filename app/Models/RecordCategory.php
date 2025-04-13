<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class RecordCategory extends Model
{
  use HasUuid;
  
  protected $fillable = [
    'record_id', 
    'category_id', 
  ];

  public function record(): BelongsTo
  {
    return $this->belongsTo(Record::class);
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }
}