<?php
namespace App\Actions\Role;
use Spatie\Permission\Models\Role;

class Create
{
  public function execute(array $data): Role
  {
    return Role::create([
      'name' => $data['name'],
      'guard_name' => $data['guard_name'] ?? 'web'
    ]);
  }
}
