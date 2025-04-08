<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\ArchiveStructure;
use App\Actions\ArchiveStructure\Get as GetArchiveStructureAction;
use App\Actions\ArchiveStructure\UpdateOrCreate as UpdateOrCreateArchiveStructureAction;
use App\Http\Resources\ArchiveStructureCategoryResource;

class ArchiveStructureController extends Controller
{
  /**
   * Get the structure of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function get(Request $request, Archive $archive)
  {
    $structure = $archive->structure()
      ->categories()
      ->with('registers')
      ->get();
    return ArchiveStructureCategoryResource::collection($structure);
  }

  /**
   * Store the structure of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function store(Request $request, Archive $archive)
  {
    (new UpdateOrCreateArchiveStructureAction())->execute($request->all(), $archive);
    $structure = $archive->structure()
      ->categories()
      ->with('registers')
      ->get();
    return ArchiveStructureCategoryResource::collection($structure);
  }
}



