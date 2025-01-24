<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Archive extends Model
{
  use SoftDeletes;
  use HasUuid;

  protected $fillable = [
    'uuid', 
    'title', 
    'acronym', 
    'media_id', 
    'company_id'
  ];

  public function records(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(Record::class);
  }

  public function media(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Media::class);
  }

  public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Company::class);
  }
}