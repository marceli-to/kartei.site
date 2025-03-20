<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Http\Resources\ArchiveResource;
use App\Actions\Archive\Get as GetArchiveAction;
use App\Models\User;

class ArchiveController extends Controller
{
  public function get(): JsonResponse
  {
    return response()->json(
      ArchiveResource::collection(
        (new GetArchiveAction())->execute()
      )
    );
  }

  /**
   * Get archives by user ID
   *
   * @param string $userId
   * @return JsonResponse
   */
  public function getByUser(string $userId): JsonResponse
  {
    $user = User::where('uuid', $userId)->firstOrFail();
    return response()->json(
      ArchiveResource::collection($user->archives)
    );
  }

  public function create(): JsonResponse
  {
  }

  public function store(Request $request): JsonResponse
  {
  }

  public function show(string $id): JsonResponse
  {
  }

  public function edit(string $id): JsonResponse
  {
  }

  public function update(Request $request, string $id): JsonResponse
  {
  }

  public function destroy(string $id): JsonResponse
  {
  }
}
