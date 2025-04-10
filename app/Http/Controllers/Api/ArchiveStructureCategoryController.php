<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Http\Resources\ArchiveStructureCategoryResource;
use App\Http\Resources\ArchiveStructureRegisterResource;

class ArchiveStructureCategoryController extends Controller
{
  /**
   * Get all categories for an archive
   * 
   * @param Archive $archive
   * @return void
   */

   public function get(Request $request, Archive $archive)
   {
     return response()->json([
       'categories' => ArchiveStructureCategoryResource::collection(
         $archive->structure()->categories()->get()
       ),
       'registers' => ArchiveStructureRegisterResource::collection(
         $archive->structure()->registers()->get()
       ),
     ]);
   }
}