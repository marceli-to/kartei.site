<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveTemplateResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'uuid' => $this->uuid,
      'image' => $this->image,
      'fields' => TemplateFieldResource::collection($this->whenLoaded('fields')),
    ];
  }
}
