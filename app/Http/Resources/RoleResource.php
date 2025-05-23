<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
  public static $wrap = null;
  
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => __('roles.' . $this->name),
    ];
  }
}