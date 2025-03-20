<?php
namespace App\Http\Requests\UserCompany;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
  /**
  * Get the validation rules that apply to the request.
  *
  * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
  */
  public function rules(): array
  {
    return [
      'name' => ['required', 'string'],
      'street' => ['required', 'string'],
      'zip' => ['required', 'string'],
      'city' => ['required', 'string'],
      'country' => ['required', 'string'],
    ];
  }
}