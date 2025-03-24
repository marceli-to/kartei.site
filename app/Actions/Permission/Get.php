<?php
namespace App\Actions\Permission;
use Spatie\Permission\Models\Permission;

class Get
{
  public function execute(): \Illuminate\Database\Eloquent\Collection
  {
    return Permission::all();
  }
}