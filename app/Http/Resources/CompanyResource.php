<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'uuid' => $this->uuid,
      'name' => $this->name,
    ];
  }
}