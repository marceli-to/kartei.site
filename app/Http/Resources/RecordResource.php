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
      'archive' => [
        'uuid' => $this->archive->uuid,
        'name' => $this->archive->name,
        'acronym' => $this->archive->acronym
      ],
      'display_number' => $this->display_number,
      'category' => $this->whenLoaded('category', function () {
        return [
          'uuid' => $this->category->uuid,
          'name' => $this->category->name,
          'number' => $this->category->number,
        ];
      }),
      'fields' => $this->whenLoaded('fields', function () {
        return $this->fields->map(function ($field) {
          return [
            'uuid' => $field->uuid,
            'content' => $field->content,
            'placeholder' => $field->placeholder,
          ];
        });
      }),
      'tags' => $this->whenLoaded('tags', function () {
        return $this->tags->pluck('uuid')->all();
      }),
      'media' => MediaResource::collection($this->media),
    ];
  }
}
