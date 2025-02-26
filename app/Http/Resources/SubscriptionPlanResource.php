<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'uuid' => $this->uuid,
      'title' => $this->title,
      'rate_monthly' => $this->rate_monthly,
      'rate_yearly' => $this->rate_yearly,
    ];
  }
}