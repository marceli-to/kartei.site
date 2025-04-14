<?php
namespace App\Actions\ArchiveSettings;
use App\Models\Archive;

class DeleteField
{
  public function execute($uuid, Archive $archive)
  {
    $settings = $archive->settings;

    $settings['record_fields'] = collect($settings['record_fields'])
      ->filter(function ($field) use ($uuid) {
        return $field['uuid'] !== $uuid;
      })
      ->values()
      ->toArray();

    $archive->settings = $settings;
    $archive->save();
  }
  
}
