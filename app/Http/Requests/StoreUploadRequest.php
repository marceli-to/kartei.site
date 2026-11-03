<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUploadRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    // Set this to `true` if you want to allow all users to upload files.
    // Alternatively, you can add authorization logic here (e.g., check user roles).
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'files' => 'required|array',
      'files.*' => [
        'required',
        'file',
        'mimes:jpeg,png,gif,webp', // Allowed file types
        'max:' . config('media.max_upload_mb') * 1024, // Max file size in KB
        'dimensions:max_width=' . config('media.max_dimension') . ',max_height=' . config('media.max_dimension'),
      ],
    ];
  }

  /**
   * Custom error messages for validation.
   */
  public function messages(): array
  {
    return [
      'files.required' => 'No files were uploaded.',
      'files.array' => 'The files must be provided as an array.',
      'files.*.required' => 'One or more files are missing.',
      'files.*.file' => 'Die Datei konnte nicht hochgeladen werden (möglicherweise zu gross).',
      'files.*.uploaded' => 'Die Datei konnte nicht hochgeladen werden (möglicherweise zu gross).',
      'files.*.mimes' => 'Only JPEG, PNG, GIF, and WEBP files are allowed.',
      'files.*.max' => 'Die Datei darf höchstens ' . config('media.max_upload_mb') . ' MB gross sein.',
      'files.*.dimensions' => 'Das Bild darf höchstens ' . config('media.max_dimension') . ' Pixel breit bzw. hoch sein.',
    ];
  }
}