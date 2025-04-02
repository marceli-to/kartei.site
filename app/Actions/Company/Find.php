<?php
namespace App\Actions\Company;
use App\Models\Company;

class Find
{
  public function execute($id, $isUuid = false): Company
  {
    if ($isUuid) {
      return Company::where('uuid', $id)->first();
    }
    return Company::find($id);
  }
}
