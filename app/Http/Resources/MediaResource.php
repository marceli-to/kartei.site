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
      'name' => $this->resized_name,
      'width' => $this->resized_width,
      'height' => $this->resized_height,
      'aspect_ratio' => $this->aspect_ratio,
      'original_name' => $this->original_name,
      'mime_type' => $this->mime_type,
      'size' => $this->size,
      'url' => $this->mediable && $this->resized_name
        ? "/storage/{$this->mediable->uuid}/{$this->resized_name}"
        : null,
      'archive' => $this->mediable?->uuid,
    ];
  }
}