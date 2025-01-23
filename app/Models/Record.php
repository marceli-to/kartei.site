<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'uuid',
    'title',
    'acronym',
    'attributes',
    'archive_id',
    'media_id',
  ];

  protected $casts = [
    'attributes' => 'array'
  ];

  public function archive()
  {
    return $this->belongsTo(Archive::class);
  }

  public function categories()
  {
    return $this->hasMany(RecordCategory::class);
  }

  public function tags()
  {
    return $this->belongsToMany(Tag::class, 'record_tags');
  }
  
  public function media()
  {
    return $this->belongsTo(Media::class);
  }

}