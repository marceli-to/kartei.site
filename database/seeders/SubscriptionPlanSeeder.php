<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use \App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $subscriptionPlans = [
      [
        'title' => 'Small',
        'rate_monthly' => 9.95,
        'rate_yearly' => 99.95
      ],
      [
        'title' => 'Medium',
        'rate_monthly' => 19.95,
        'rate_yearly' => 199.95
      ],
      [
        'title' => 'Professional',
        'rate_monthly' => 49.95,
        'rate_yearly' => 499.95
      ]
    ];

    foreach ($subscriptionPlans as $plan)
    {
      SubscriptionPlan::create($plan);
    }
  }
}
