<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class UserSubscription extends Model
{
  use SoftDeletes;
  use HasUuid;

  protected $fillable = [
    'uuid',
    'user_id', 
    'subscription_plan_id', 
    'payment_interval', 
    'payment_method',
    'starts_at', 
    'ends_at'
  ];

  protected $dates = [
    'starts_at',
    'ends_at'
  ];

  public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
  }
  
  public function isActive(): bool
  {
    return $this->deleted_at === NULL && ($this->ends_at === null || $this->ends_at > now());
  }
}