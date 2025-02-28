<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserThemeResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'color_scheme' => $this['color_scheme'] ?? null,
      'color_theme' => $this['color_theme'] ?? null
    ];
  }
}