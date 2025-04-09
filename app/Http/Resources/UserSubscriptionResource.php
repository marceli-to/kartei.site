<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'uuid' => $this->uuid,
      'payment_interval' => $this->payment_interval,
      'payment_method' => $this->payment_method,
      'max_users' => $this->plan->max_users,
      'starts_at' => $this->starts_at,
      'ends_at' => $this->ends_at,
      'plan' => [
        'uuid' => $this->plan->uuid,
        'title' => $this->plan->title,
      ],
    ];
  }
}