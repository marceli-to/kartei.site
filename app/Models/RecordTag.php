<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RecordTag extends Pivot
{
  protected $table = 'record_tag';

  public function record(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Record::class);
  }

  public function tag(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Tag::class);
  }
}
