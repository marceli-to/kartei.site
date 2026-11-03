<?php
namespace App\Http\Requests\ArchiveUser;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
      'email' => array_filter([
        'required',
        'email',
        'max:255',
        // Creating with a known email reuses that account (User\Create);
        // changing a user's email to a taken one is an error.
        // Soft-deleted users count too, they keep their address.
        $this->route('user') ? Rule::unique(User::class)->ignore($this->route('user')) : null,
      ]),
    ];
  }
}
