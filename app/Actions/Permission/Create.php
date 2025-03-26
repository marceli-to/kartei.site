<?php
namespace App\Actions\Permission;
use Spatie\Permission\Models\Permission;

class Create
{
  public function execute(array $data): Permission
  {
    return Permission::firstOrCreate($data);
  }
}
