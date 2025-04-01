<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Http\Resources\ArchiveResource;
use App\Actions\Archive\Get as GetArchiveAction;
use App\Actions\Archive\Create as CreateArchiveAction;
use App\Actions\ArchiveImage\Create as CreateImageAction;
use App\Actions\ArchiveUser\Attach as AttachArchiveUserAction;
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
   * @param string $userId
   * @return JsonResponse
   */
  public function getByUser(string $userId): JsonResponse
  {
    return response()->json(
      ArchiveResource::collection(
        (new GetArchiveAction())->execute(['user_id' => $userId])
      )
    );
  }

  /**
   * Create a new archive
   *
   * @param array $data
   * @return JsonResponse
   */
  public function create(Request $request): JsonResponse
  {
    $archive = (new CreateArchiveAction())->execute($request->except('image'));
    (new CreateImageAction())->execute($request->image, $archive);
    (new AttachArchiveUserAction())->execute(auth()->user(), $archive->id);
    return response()->json(new ArchiveResource($archive));
  }
}
