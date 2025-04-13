<?php
namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'uuid' => $this->uuid
    ];
  }
}
