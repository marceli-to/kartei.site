<?php
namespace App\Actions\Archive;
use App\Models\Archive;
use App\Actions\Company\Find as FindCompanyAction;

class Create
{
  public function execute(array $data): Archive
  {
    $company = (isset($data['company'])) ? (new FindCompanyAction())->execute($data['company'], true) : null;
    return Archive::create([
      'name' => $data['name'],
      'acronym' => $data['acronym'],
      'company_id' => $company?->id ?? null
    ]);
  }
}
