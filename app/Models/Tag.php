<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Tag extends Model
{
  use HasUuid;
    
  protected $fillable = [
    'uuid', 
    'name',
    'archive_id'
  ];

  public function records(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
    return $this->belongsToMany(Record::class, 'record_tags');
  }

  public function archive(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Archive::class);
  }
}