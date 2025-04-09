<?php
namespace App\Actions\TemplateField;
use App\Models\TemplateField;
use App\Models\ArchiveTemplate;

class UpdateOrCreate
{
  public function execute($fields, ArchiveTemplate $archiveTemplate): bool
  {
    foreach ($fields as $field) {
      TemplateField::updateOrCreate(
        [
          'uuid' => $field['uuid'],
          'archive_template_id' => $archiveTemplate->id,
        ],
        [
          'placeholder' => $field['placeholder'],
        ]
      );
    }
    return true;
  }
}
