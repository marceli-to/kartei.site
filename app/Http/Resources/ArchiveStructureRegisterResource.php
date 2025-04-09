<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveStructureRegisterResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'number' => $this->number,
      'title' => $this->title,
      'custom_id' => $this->custom_id,
      'numeral_type' => $this->numeral_type,
      'order' => $this->order,
      'uuid' => $this->uuid
    ];
  }
}
