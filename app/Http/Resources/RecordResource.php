<?php
namespace App\Http\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'uuid' => $this->uuid,
      'archive_acronym' => $this->archive->acronym,
      'display_number' => $this->display_number,
      'category' => $this->whenLoaded('category', function () {
        return [
          'uuid' => $this->category->uuid,
          'name' => $this->category->name,
          'number' => $this->category->number,
          'is_category' => $this->category->isCategory(),
          'is_register' => $this->category->isRegister()
        ];
      }),
      'fields' => $this->whenLoaded('fields', function () {
        return $this->fields->map(function ($field) {
          return [
            'content' => $field->content,
          ];
        });
      }),

      'tags' => $this->whenLoaded('tags'),
      'media' => MediaResource::collection($this->media),
    ];
  }
}
