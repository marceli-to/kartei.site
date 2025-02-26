<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'user_id', 
    'subscription_plan_id', 
    'payment_interval', 
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