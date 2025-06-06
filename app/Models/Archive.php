<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
    'settings',
    'company_id'
  ];

  protected $casts = [
    'settings' => 'array',
  ];

  public function records(): HasMany
  {
    return $this->hasMany(Record::class);
  }

  public function company(): BelongsTo
  {
    return $this->belongsTo(Company::class);
  }

  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class);
  }

  public function media(): MorphOne
  {
    return $this->morphOne(Media::class, 'mediable');
  }

  public function tags(): HasMany
  {
    return $this->hasMany(Tag::class);
  }
  
  public function categories(): HasMany
  {
    return $this->hasMany(Category::class)->whereNull('parent_id')->orderBy('order');
  }

  public function registers(): HasMany
  {
    return $this->hasMany(Category::class)->whereNotNull('parent_id')->orderBy('order');
  }

  public function setAcronymAttribute($value)
  {
    $this->attributes['acronym'] = strtoupper($value);
  }

  public function getAcronymAttribute($value)
  {
    return strtoupper($value);
  }

  protected static function getSlugSource(): string
  {
    return 'name';
  }

  protected static function booted()
  {
    static::creating(function ($archive) {
      if (is_null($archive->settings)) {
        $archive->settings = [
          'has_images' => true,
          'record_fields' => [],
        ];
      }
    });
  }
}