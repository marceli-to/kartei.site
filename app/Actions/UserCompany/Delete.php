<?php
namespace App\Actions\UserCompany;
use App\Models\Company;

class Delete
{
  public function execute(Company $company): void
  {
    $company->delete();
  }
}
