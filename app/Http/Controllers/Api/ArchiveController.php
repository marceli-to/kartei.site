<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Http\Resources\ArchiveResource;
use App\Actions\Archive\Get as GetArchiveAction;
use App\Actions\Archive\Find as FindArchiveAction;
use App\Actions\Archive\Create as CreateArchiveAction;
use App\Actions\Archive\Update as UpdateArchiveAction;
use App\Actions\Archive\Delete as DeleteArchiveAction;
use App\Actions\ArchiveImage\Create as CreateArchiveImageAction;
use App\Actions\ArchiveImage\Delete as DeleteArchiveImageAction;
use App\Actions\ArchiveUser\Attach as AttachArchiveUserAction;
use App\Http\Requests\Archive\StoreRequest;
use App\Models\User;

class ArchiveController extends Controller
{
  /**
   * Get archives based on authenticated user's role
   *
   * @return JsonResponse
   */
  public function get(): JsonResponse
  {
    return response()->json(
      ArchiveResource::collection(
        (new GetArchiveAction())->execute()
      )
    );
  }

  /**
   * Get all archives (Super Admin only)
   *
   * @return JsonResponse
   */
  public function getAll(): JsonResponse
  {
    return response()->json(
      ArchiveResource::collection(
        (new GetArchiveAction())->execute(['all' => true])
      )
    );
  }

  /**
   * Get archives for the authenticated admin
   *
   * @return JsonResponse
   */
  public function getByAdmin(): JsonResponse
  {
    return response()->json(
      ArchiveResource::collection(
        (new GetArchiveAction())->execute(['admin' => true])
      )
    );
  }

  /**
   * Get archives for a specific user
   *
   * @param User $user
   * @return JsonResponse
   */
  public function getByUser(User $user): JsonResponse
  {
    $archives = (new GetArchiveAction())->execute(['user_id' => $user->id]);
    $archives->load([
      'company',
      'tags',
      'categories' => function ($query) {
        $query->categories()->with('registers');
      }
    ]);
    return response()->json(
      ArchiveResource::collection(
        $archives
      )
    );
  }

  /**
   * Get a specific archive by UUID
   *
   * @param Archive $archive
   * @return JsonResponse
   */
  public function find(Archive $archive): JsonResponse
  {
    return response()->json(
      new ArchiveResource(
        $archive->load('company', 'media')
      )
    );
  }

  /**
   * Create a new archive
   *
   * @param StoreRequest $request
   * @return JsonResponse
   */
  public function create(StoreRequest $request): JsonResponse
  {
    $archive = (new CreateArchiveAction())->execute($request->except('image'));
    (new CreateArchiveImageAction())->execute($request->image[0], $archive);
    (new AttachArchiveUserAction())->execute(auth()->user(), $archive->id);
    return response()->json(
      new ArchiveResource($archive->load('company', 'media'))
    );
  }

  /**
   * Update an existing archive
   *
   * @param StoreRequest $request
   * @param Archive $archive
   * @return JsonResponse
   */
  public function update(StoreRequest $request, Archive $archive): JsonResponse
  {
    $archive = (new UpdateArchiveAction())->execute($request->except('image'), $archive);
    (new CreateArchiveImageAction())->execute($request->image[0], $archive);
    return response()->json(
      new ArchiveResource($archive->load('company', 'media'))
    );
  }

  /**
   * Delete an archive
   *
   * @param Archive $archive
   * @return JsonResponse
   */
  public function destroy(Archive $archive): JsonResponse
  {
    (new DeleteArchiveAction())->execute($archive);
    if ($archive->media) {
      (new DeleteArchiveImageAction())->execute($archive);
    }
    return response()->json([
      'success' => true
    ]);
  }
}
