<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class SubscriptionPlan extends Model
{
  use HasUuid;
  
  protected $fillable = [
    'uuid',
    'title',
    'max_users',
    'rate_monthly',
    'rate_yearly',
  ];

  public function userSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
  {
    return $this->hasMany(UserSubscription::class);
  }
  
  /**
   * Helper to get price based on interval
   * 
   * @param string $interval 'monthly' or 'yearly'
   * @return decimal
   * 
   * Usage:
   * $plan = SubscriptionPlan::findOrFail($request->plan_id);
   * $interval = $request->interval; // 'monthly' or 'yearly'
   * $price = $plan->getPriceForInterval($interval);
   */
  public function getPriceForInterval($interval): decimal
  {
    return $interval === 'yearly' ? $this->rate_yearly : $this->rate_monthly;
  }
}
