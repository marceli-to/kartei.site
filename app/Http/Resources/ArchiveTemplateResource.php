<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveTemplateResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'uuid' => $this->uuid ?? null,
      'image' => $this->image ?? 1,
      'fields' => TemplateFieldResource::collection($this->fields ?? []),
    ];
  }
}
