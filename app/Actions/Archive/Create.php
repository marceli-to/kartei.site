<?php
namespace App\Actions\Archive;
use App\Models\Archive;

class Create
{
  public function execute(array $data): Archive
  {
    return Archive::create([
      'name' => $data['name'],
      'acronym' => $data['acronym'],
      'company_id' => $data['company_id'] ?? null
    ]);
  }
}
