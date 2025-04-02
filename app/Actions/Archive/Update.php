<?php
namespace App\Actions\Archive;
use App\Models\Archive;
use App\Actions\Company\Find as FindCompanyAction;

class Update
{
  public function execute(array $data, Archive $archive): Archive
  {
    $company = (isset($data['company'])) ? (new FindCompanyAction())->execute($data['company'], true) : null;
    $data['company_id'] = $company?->id ?? null;
    $archive->update($data);
    return $archive;
  }
}
