<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use App\Traits\Sluggable;

class Archive extends Model
{
  use SoftDeletes;
  use HasUuid;
  use Sluggable;

  protected $fillable = [
    'uuid', 
    'slug',
    'title', 
    'acronym', 
    'company_id'
  ];

  public function records(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(Record::class);
  }

  public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Company::class);
  }

  public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
    return $this->belongsToMany(User::class);
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