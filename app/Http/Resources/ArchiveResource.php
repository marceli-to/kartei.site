<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MediaResource;

class ArchiveResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'uuid' => $this->uuid,
      'acronym' => $this->acronym,
      'name' => $this->name,
      'company' => $this->whenLoaded('company', function () {
        return $this->company ? [
          'uuid' => $this->company->uuid,
          'name' => $this->company->name,
        ] : null;
      }),
      'image' => MediaResource::make($this->media),
    ];
  }
}
