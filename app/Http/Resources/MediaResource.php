<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'uuid' => $this->uuid,
      'resized_name' => $this->resized_name,
      'resized_width' => $this->resized_width,
      'resized_height' => $this->resized_height,
      'aspect_ratio' => $this->aspect_ratio,
      'archive' => $this->mediable?->uuid
    ];
  }
}