<?php
namespace App\Actions\UserSubscription;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

class Update
{
  public function execute(array $data, User $user): UserSubscription
  {

    $plan = SubscriptionPlan::where('uuid', $data['subscription'])->first();

    if (!$plan) {
      throw new \Exception('Subscription plan not found');
    }

    $userSubscription = UserSubscription::firstOrCreate(
      [
        'user_id' => $user->id,
      ],
      [
        'subscription_plan_id' => $plan->id,
        'payment_interval' => $data['payment_interval'],
        'payment_method' => $data['payment_method'],
        'starts_at' => now(),
      ]
    );
    
    // If the address already existed, update its fields
    if (!$userSubscription->wasRecentlyCreated) {
      $userSubscription->fill([
        'subscription_plan_id' => $plan->id,
        'payment_interval' => $data['payment_interval'],
        'payment_method' => $data['payment_method'],
      ]);
      $userSubscription->save();
    }
    
    return $userSubscription;
  }
}