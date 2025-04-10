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
    'company_id'
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
  
  public function structure(): HasMany
  {
    return $this->hasMany(ArchiveStructure::class);
  }

  public function structureCategories(): HasMany
  {
    return $this->hasMany(ArchiveStructureCategory::class)->where('parent_id', null);
  }

  public function structureRegisters(): HasMany
  {
    return $this->hasMany(ArchiveStructureRegister::class)->where('parent_id', null);
  }

  public function template(): HasOne
  {
    return $this->hasOne(ArchiveTemplate::class);
  }

  protected static function getSlugSource(): string
  {
    return 'name';
  }
}