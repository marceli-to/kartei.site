<?php
namespace App\Actions\Permission;
use Spatie\Permission\Models\Permission;

class Create
{
  public function execute(array $data): Permission
  {
    return Permission::create([
      'name' => $data['name'],
      'guard_name' => $data['guard_name'] ?? 'web',
      'publish' => $data['publish'] ?? false
    ]);
  }
}
