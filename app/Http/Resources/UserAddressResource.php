<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'uuid' => $this->uuid ?? null,
      'company' => $this->company ?? null,
      'byline' => $this->byline ?? null,
      'street' => $this->street ?? null,
      'street_number' => $this->street_number ?? null,
      'zip' => $this->zip ?? null,
      'city' => $this->city ?? null,
      'country' => $this->country ?? 'Schweiz',
    ];
  }
}