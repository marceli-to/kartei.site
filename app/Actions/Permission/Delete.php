<?php
namespace App\Actions\Permission;
use Spatie\Permission\Models\Permission;

class Delete
{
  public static function execute(Permission $permission): void
  {
    $permission->delete();
  }
}