<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RecordCategory extends Model
{
  protected $fillable = [
    'uuid', 
    'number', 
    'acronym', 
    'record_id', 
    'numeral_id', 
    'parent_id'
  ];

  public function record()
  {
    return $this->belongsTo(Record::class);
  }

  public function numeral()
  {
    return $this->belongsTo(Numeral::class);
  }

  public function parent()
  {
    return $this->belongsTo(RecordCategory::class, 'parent_id');
  }

  public function children()
  {
    return $this->hasMany(RecordCategory::class, 'parent_id');
  }
}