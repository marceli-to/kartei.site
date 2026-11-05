<?php
namespace App\Http\Requests\Record;

class StoreRequest extends UpdateRequest
{
  public function rules(): array
  {
    return [
      'category' => 'required',
      ...parent::rules(),
    ];
  }
  
  public function messages(): array
  {
    return [
      'category.required' => 'Kategorie oder Register fehlt',
      ...parent::messages(),
    ];
  }
}
  