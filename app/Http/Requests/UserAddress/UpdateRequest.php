<?php
namespace App\Http\Requests\UserAddress;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  /**
  * Get the validation rules that apply to the request.
  *
  * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
  */
  public function rules(): array
  {
    return [
      'company' => ['nullable', 'string', 'max:255'],
      'byline' => ['nullable', 'string', 'max:255'],
      'street' => ['required', 'string', 'max:255'],
      'street_number' => ['nullable', 'string', 'max:20'],
      'zip' => ['required', 'string', 'max:20'],
      'city' => ['required', 'string', 'max:255'],
      'country' => ['nullable', 'string', 'max:255'],
    ];
  }
}