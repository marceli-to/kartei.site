<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Traits\Sluggable;

class Archive extends Model
{
  use HasUuid;
  use Sluggable;

  protected $fillable = [
    'uuid', 
    'slug',
    'name', 
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
    return $this->morphOne(Media::class, 'mediable');
  }

  protected static function getSlugSource(): string
  {
    return 'name';
  }
}