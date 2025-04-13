<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\ArchiveTemplate;
use App\Actions\ArchiveTemplate\Get as GetArchiveTemplateAction;
use App\Actions\ArchiveTemplate\UpdateOrCreate as UpdateOrCreateArchiveTemplateAction;
use App\Actions\TemplateField\UpdateOrCreate as UpdateOrCreateTemplateFieldAction;
use App\Http\Resources\ArchiveTemplateResource;

class ArchiveTemplateController extends Controller
{
  /**
   * Get the template of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function get(Request $request, Archive $archive)
  {
    $archiveTemplate = (new GetArchiveTemplateAction())->execute($archive);
    return new ArchiveTemplateResource($archiveTemplate);
  }

  /**
   * Store the template of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function store(Request $request, Archive $archive)
  {
    $archiveTemplate = (new UpdateOrCreateArchiveTemplateAction())->execute(
      $request->except('fields'), 
      $archive
    );

    (new UpdateOrCreateTemplateFieldAction())->execute(
      $request->fields,
      $archiveTemplate
    );
    $archiveTemplate->load('fields');
    return new ArchiveTemplateResource($archiveTemplate);
  }
}



