<?php
namespace App\Actions\Permission;
use Spatie\Permission\Models\Permission;

class Find
{
  public function execute($id): Permission
  {
    return Permission::find($id);
  }
}
