<?php
namespace App\Http\Requests\ArchiveUser;
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
      'firstname' => 'required|string|max:255',
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',
    ];
  }
}
  