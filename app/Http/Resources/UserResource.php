<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  public static $wrap = null;

  public function toArray(Request $request): array
  {
    return [
      'user' => [
        'firstname' => $this->firstname,
        'name' => $this->name,
        'email' => $this->email,
      ],
      'roles' => RoleResource::collection($this->roles),
      'permissions' => $this->getPermissionsViaRoles()
        ->merge($this->permissions)
        ->map(fn($permission) => [
          'name' => $permission->name
        ])
        ->unique('name')
        ->values(),
    ];
  }
}