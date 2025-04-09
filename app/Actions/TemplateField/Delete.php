<?php
namespace App\Actions\TemplateField;
use App\Models\TemplateField;

class Delete
{
  public function execute(TemplateField $templateField)
  { 
    return $templateField->delete();
  }
}
