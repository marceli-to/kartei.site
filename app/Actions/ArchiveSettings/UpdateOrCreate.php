<?php
namespace App\Actions\ArchiveSettings;
use App\Models\Archive;

class UpdateOrCreate
{
  public function execute($data, Archive $archive): Archive
  {
    $settings = $archive->settings ?? [];
    $settings['has_images'] = $data['has_images'];

    // Convert existing fields to associative array by UUID for easy lookup
    $existingFields = collect($settings['record_fields'] ?? [])->keyBy('uuid');

    $updatedFields = [];

    foreach ($data['record_fields'] as $field) {
      // If the incoming field has a UUID, try to update the matching one
      if (!empty($field['uuid']) && $existingFields->has($field['uuid'])) {
        $existing = $existingFields[$field['uuid']];
        $updatedFields[] = array_merge($existing, [
          'placeholder' => $field['placeholder'],
          'order' => $field['order'],
        ]);
      } 
      else {
        // New field — assign UUID and add to list
        $updatedFields[] = [
          'uuid' => \Str::uuid()->toString(),
          'placeholder' => $field['placeholder'],
          'order' => $field['order'],
        ];
      }
    }

    $settings['record_fields'] = $updatedFields;
    $archive->settings = $settings;
    $archive->save();

    return $archive;
  }
  
  
}

