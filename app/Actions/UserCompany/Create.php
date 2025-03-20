<?php
namespace App\Actions\UserCompany;
use App\Models\Company;

class Create
{
  public function execute(array $data): Company
  {
    $company = Company::create($data);
    auth()->user()->companies()->attach($company->id);
    return $company;
  }
}
