<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archive extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'uuid', 
    'title', 
    'acronym', 
    'media_id', 
    'company_id'
  ];

  public function records()
  {
    return $this->hasMany(Record::class);
  }

  public function media()
  {
    return $this->belongsTo(Media::class);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}