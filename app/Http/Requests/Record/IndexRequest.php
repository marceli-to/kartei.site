<?php
namespace App\Http\Requests\Record;
use App\Actions\Record\Get;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
  public function authorize(): bool
  {
    // The controller authorizes against the archive
    return true;
  }

  public function rules(): array
  {
    return [
      'page' => ['nullable', 'integer', 'min:1'],
      'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
      'search' => ['nullable', 'string', 'max:255'],
      'sort' => ['nullable', Rule::in(Get::SORT_ORDERS)],
      'categories' => ['nullable', 'array'],
      'categories.*' => ['string'],
      'registers' => ['nullable', 'array'],
      'registers.*' => ['string'],
      'tags' => ['nullable', 'array'],
      'tags.*' => ['string'],
    ];
  }
}
