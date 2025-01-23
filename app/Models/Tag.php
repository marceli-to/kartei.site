<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
  use SoftDeletes;
  
  protected $fillable = [
    'uuid', 
    'title'
  ];

  public function records()
  {
    return $this->belongsToMany(Record::class, 'record_tags');
  }
}