<?php
namespace App\Http\Requests\User;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UpdatePasswordRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
   */
  public function rules(): array
  {
    return [
      'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
        if (!Hash::check($value, auth()->user()->password)) {
          $fail('Derzeitiges Passwort stimmt nicht überein.');
        }
      }],
      'new_password' => [
        'required', 
        'string',
        Password::min(8)
          ->letters()
          ->mixedCase()
          ->numbers()
          ->symbols(),
        'confirmed'
      ],
      'new_password_confirmation' => ['required', 'string'],
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'current_password.required' => 'Derzeitiges Passwort muss ausgefüllt sein.',
      'current_password.string' => 'Derzeitiges Passwort muss eine Zeichenkette sein.',
      // This is a custom error from your callback, not from 'confirmed' rule
      //'current_password.confirmed' => 'Derzeitiges Passwort stimmt nicht überein.',
      
      'new_password.required' => 'Neues Passwort muss ausgefüllt sein.',
      'new_password.string' => 'Neues Passwort muss eine Zeichenkette sein.',
      'new_password.min' => 'Neues Passwort muss mindestens :min Zeichen lang sein.',
      'new_password.letters' => 'Neues Passwort muss Buchstaben enthalten.',
      'new_password.mixed_case' => 'Neues Passwort muss mindestens einen Gross- und einen Kleinbuchstaben enthalten.',
      'new_password.numbers' => 'Neues Passwort muss mindestens eine Zahl enthalten.',
      'new_password.symbols' => 'Neues Passwort muss mindestens ein Symbol enthalten.',
      'new_password.confirmed' => 'Passwortbestätigung stimmt nicht überein.',
      'new_password_confirmation.required' => 'Neue Passwortbestätigung muss ausgefüllt sein.',
    ];
  }

  /**
   * Get custom attributes for validator errors.
   *
   * @return array<string, string>
   */
  public function attributes(): array
  {
    return [
      'current_password' => 'Derzeitiges Passwort',
      'new_password' => 'Neues Passwort',
      'new_password_confirmation' => 'Neues Passwort (Bestätigung)'
    ];
  }
}