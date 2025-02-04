<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Record extends Model
{
  use SoftDeletes;
  use HasUuid;

  protected $fillable = [
    'uuid',
    'title',
    'acronym',
    'attributes',
    'archive_id',
  ];

  protected $casts = [
    'attributes' => 'array'
  ];

  public function archive(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Archive::class);
  }

  public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(RecordCategory::class);
  }

  public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
    return $this->belongsToMany(Tag::class, 'record_tags');
  }
  
  public function media()
  {
    return $this->morphMany(Media::class, 'mediable');
  }
}