<?php
namespace App\Http\Requests\UserPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StoreRequest extends FormRequest
{
  /**
   * Roles an archive admin may hand out. "Admin" and "Super Admin"
   * are account-level roles and can never be assigned through the API.
   */
  public const ASSIGNABLE_ROLES = ['Manager', 'Archivist', 'Viewer'];

  public function rules(): array
  {
    return [
      'archive' => ['required', 'string', Rule::exists('archives', 'uuid')],
      'role' => [
        'required',
        'integer',
        Rule::in(Role::whereIn('name', self::ASSIGNABLE_ROLES)->pluck('id')->all()),
      ],
      'permissions' => ['present', 'array'],
      // Only published (base) permissions can be granted per archive
      'permissions.*' => [
        'integer',
        Rule::exists('permissions', 'id')->where('publish', true),
      ],
    ];
  }
}
