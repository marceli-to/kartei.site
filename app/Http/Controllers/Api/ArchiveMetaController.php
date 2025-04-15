<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Actions\ArchiveSettings\Get as GetArchiveSettingsAction;
use App\Actions\Tag\Get as GetTagAction;
use App\Http\Resources\TagResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RegisterResource;

class ArchiveMetaController extends Controller
{
  /**
   * Get the meta of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function get(Request $request, Archive $archive)
  {
    $settings = (new GetArchiveSettingsAction())->execute(
      $archive
    );

    $categories = CategoryResource::collection(
      $archive->categories()->get()
    );

    $registers = RegisterResource::collection(
      $archive->registers()->get()
    );

     $categoriesRegisters = CategoryResource::collection(
      $archive->categories()->with('registers')->get()
    );

    $tags = TagResource::collection(
      (new GetTagAction())->execute($archive)
    );

    return response()->json([
      'settings' => $settings,
      'categories' => $categories,
      'registers' => $registers,
      'categories_registers' => $categoriesRegisters,
      'tags' => $tags
    ]);
  }
}
