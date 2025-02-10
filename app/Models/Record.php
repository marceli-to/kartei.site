<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use App\Traits\Sluggable;

class Record extends Model
{
  use SoftDeletes;
  use HasUuid;
  use Sluggable;

  protected $fillable = [
    'uuid',
    'slug',
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

  protected static function getSlugSource(): string
  {
    return 'title';
  }
}