<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Subscription extends Model
{
  use SoftDeletes;
  use HasUuid;
  
  protected $fillable = [
    'uuid',
    'name',
  ];

  public function companies(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(Company::class);
  }
}
