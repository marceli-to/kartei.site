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
      'name' => $this->name,
      'custom_id' => $this->custom_id,
      'numeral_type' => $this->numeral_type,
      'custom_id_type' => $this->custom_id_type,
      'order' => $this->order,
      'uuid' => $this->uuid,
      'registers' => ArchiveStructureRegisterResource::collection(
        $this->whenLoaded('registers')
      )
    ];
  }
}
