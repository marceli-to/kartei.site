<?php
namespace App\Actions\UserCompany;
use App\Models\Company;

class Update
{
  public function execute(Company $company, array $data): Company
  {
    $company->update($data);
    return $company;
  }
}
