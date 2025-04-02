<?php
namespace App\Http\Requests\Archive;
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
      'name' => 'required|string|max:255',
      'acronym' => 'required|string|max:5',
      'image' => 'required|array|size:1',
      'image.0.original.original_name' => 'required|string|max:255',
      'image.0.original.name' => 'required|string|max:255',
      'image.0.original.mime_type' => 'required|string|in:image/png,image/jpg,image/jpeg,image/gif',
      'image.0.original.size' => 'required|integer|max:5120000', // max ~5MB
    ];
  }
  

  public function messages(): array
  {
    return [
      'image.required' => 'Titelbild wird benötigt',
    ];
  }
}
  