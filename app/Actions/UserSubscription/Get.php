<?php
namespace App\Actions\UserSubscription;
use App\Models\UserSubscription;
use App\Models\User;

class Get
{
  public function execute(User $user): ?UserSubscription
  {
    return $user->subscription()->with('plan')->first();
  }
}
