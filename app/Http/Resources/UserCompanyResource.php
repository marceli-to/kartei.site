<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCompanyResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'uuid' => $this->uuid,
      'name' => $this->name,
      'street' => $this->street,
      'street_number' => $this->street_number,
      'zip' => $this->zip,
      'city' => $this->city,
      'country' => $this->country,
    ];
  }
}
