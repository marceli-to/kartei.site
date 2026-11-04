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
      // name is the key the SPA checks (e.g. "Super Admin"), label is for display
      'name' => $this->name,
      'label' => __('roles.' . $this->name),
    ];
  }
}