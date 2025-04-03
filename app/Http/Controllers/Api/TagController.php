<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Archive;
use App\Actions\Tag\Create as CreateTagAction;
use App\Actions\Tag\Get as GetTagAction;
use App\Actions\Tag\UpdateOrCreate as UpdateOrCreateTagAction;
use App\Actions\Tag\Delete as DeleteTagAction;

use App\Http\Resources\TagResource;

class TagController extends Controller
{
  /**
   * Get all tags
   * 
   * @param Archive $archive 
   */
  public function get(Archive $archive): AnonymousResourceCollection
  {
    return TagResource::collection(
      (new GetTagAction())->execute($archive)
    );
  }

  public function store(Request $request, Archive $archive): AnonymousResourceCollection
  {
    (new UpdateOrCreateTagAction())->execute(
      $request->tags,
      $archive
    );
    return TagResource::collection($archive->tags);
  }
  /**
   * Delete a tag
   * 
   * @param Tag $tag
   */
  public function destroy(Tag $tag)
  {
    (new DeleteTagAction())->execute($tag);
    return response()->json([
      'success' => true
    ]);
  }
  
}
