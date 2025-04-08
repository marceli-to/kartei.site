<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ArchiveStructureRegisterResource;

class ArchiveStructureCategoryResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'number' => $this->number,
      'title' => $this->title,
      'custom_id' => $this->custom_id,
      'order' => $this->order,
      'uuid' => $this->uuid,
      'registers' => ArchiveStructureRegisterResource::collection(
        $this->whenLoaded('registers')
      )
    ];
  }
}
