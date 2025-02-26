<?php
namespace App\Actions\SubscriptionPlan;
use App\Models\SubscriptionPlan;

class Get
{
  public function execute()
  {
    return SubscriptionPlan::all();
  }
}
