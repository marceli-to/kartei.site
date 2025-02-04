<?php
namespace App\Actions\Archive;
use App\Models\Archive;

class Create
{
  public function execute(array $data): Archive
  {
    return Archive::create([
      'title' => $data['title'],
      'acronym' => $data['acronym'],
      'media_id' => $data['media_id'] ?? null,
      'company_id' => $data['company_id'] ?? null
    ]);
  }
}
