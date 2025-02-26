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
      'street' => ['required', 'string'],
      'zip' => ['required', 'string'],
      'city' => ['required', 'string'],
    ];
  }
}