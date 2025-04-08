<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveUsersResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    // Get the role through the pivot
    $role = $this->roles->first();

    return [
      'uuid' => $this->uuid,
      'firstname' => $this->firstname,
      'name' => $this->name,
      'email' => $this->email,
      'role' => $role ? __('roles.' . $role->name) : null
    ];
  }
}