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
      'uuid' => $this->uuid,
      'acronym' => $this->acronym,
      'name' => $this->name,
      'company' => CompanyResource::make($this->whenLoaded('company')),
      'tags' => TagResource::collection($this->whenLoaded('tags')),
      'categories' => CategoryResource::collection(
        $this->whenLoaded('categories')
      ),
      'image' => MediaResource::make($this->media),
    ];
  }
}
