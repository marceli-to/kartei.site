<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Actions\ArchiveSettings\Get as GetArchiveSettingsAction;
use App\Actions\ArchiveSettings\UpdateOrCreate as UpdateOrCreateArchiveSettingsAction;
use App\Actions\ArchiveSettings\DeleteField as DeleteFieldAction;

class ArchiveSettingsController extends Controller
{
  /**
   * Get the settings of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function get(Request $request, Archive $archive)
  {
    $this->authorize('view', $archive);
    $archiveSettings = (new GetArchiveSettingsAction())->execute($archive);
    return response()->json($archiveSettings);
  }

  /**
   * Store the settings of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function store(Request $request, Archive $archive)
  {
    $this->authorize('manageTemplate', $archive);
    $data = $request->validate([
      'has_images' => ['sometimes', 'boolean'],
      'record_fields' => ['sometimes', 'array'],
      'record_fields.*.uuid' => ['nullable', 'string'],
      'record_fields.*.placeholder' => ['nullable', 'string', 'max:255'],
      'record_fields.*.order' => ['nullable', 'integer'],
    ]);
    $archive = (new UpdateOrCreateArchiveSettingsAction())->execute($data, $archive);
    return response()->json($archive->settings);
  }

  /**
   * Delete a settings record_field
   * 
   * @param Archive $archive
   * @param String $uuid
   */
  public function destroy($uuid, Archive $archive)
  {
    $this->authorize('manageTemplate', $archive);
    (new DeleteFieldAction())->execute($uuid, $archive);
    return response()->json([
      'success' => true
    ]);
  }

}



