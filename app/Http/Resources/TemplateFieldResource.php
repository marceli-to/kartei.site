<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateFieldResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'uuid' => $this->uuid,
      'placeholder' => $this->placeholder,
      'order' => $this->order
    ];
  }
}
