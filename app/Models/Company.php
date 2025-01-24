<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Company extends Model
{
  use SoftDeletes;
  use HasUuid;

  protected $fillable = [
    'uuid',
    'name',
    'street',
    'street_number',
    'zip',
    'city',
    'subscription_id',
  ];

  public function subscription(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Subscription::class);
  }}
