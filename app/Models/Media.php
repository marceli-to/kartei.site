<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'uuid',
    'name',
    'original_name',
    'mime_type',
    'size'
  ];
}