<?php
namespace App\Http\Requests\Record;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'category' => 'required',
    ];
  }
  
  public function messages(): array
  {
    return [
      'category.required' => 'Kategorie oder Register fehlt',
    ];
  }
}
  