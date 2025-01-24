<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
  use SoftDeletes;
  
  protected $fillable = [
    'uuid',
    'title',
    'street',
    'street_number',
    'zip',
    'city',
    'subscription_id',
    'media_id',
  ];

  public function subscription(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Subscription::class);
  }}
