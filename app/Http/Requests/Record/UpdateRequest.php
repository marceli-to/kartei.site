<?php
namespace App\Http\Requests\Record;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  // record_fields.content is a string column
  public const FIELD_MAX_LENGTH = 255;

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'fields' => ['nullable', 'array'],
      'fields.*.content' => ['nullable', 'string', 'max:' . self::FIELD_MAX_LENGTH],
    ];
  }

  public function messages(): array
  {
    return [
      'fields.*.content.max' => 'Ein Beschreibungsfeld darf höchstens :max Zeichen enthalten.',
    ];
  }
}
