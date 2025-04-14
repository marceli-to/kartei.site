<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RecordField extends Model
{
  protected $fillable = [
    'uuid',
    'placeholder',
    'content',
    'record_id'
  ];

  public function record(): BelongsTo
  {
    return $this->belongsTo(Record::class);
  }
}
