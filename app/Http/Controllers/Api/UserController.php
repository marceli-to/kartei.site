<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Actions\User\Update as UpdateUserAction;
use App\Actions\User\UpdatePassword as UpdatePasswordAction;
use App\Actions\User\Delete as DeleteUserAction;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function find(Request $request): UserResource
  {
    return new UserResource($request->user());
  }

  public function update(UpdateRequest $request): JsonResponse
  {
    $user = (new UpdateUserAction())->execute(
      $request->validated(), 
      auth()->user()
    );

    return response()->json([
      'user' => $user,
      'email_changed' => $user->wasChanged('email')
    ]);
  }

  public function password(UpdatePasswordRequest $request): Response
  {
    (new UpdatePasswordAction())->execute(
      $request->all(), 
      auth()->user()
    );
    return response()->noContent();
  }

  /**
   * Archives that are deleted together with the account (shown before deleting it)
   */
  public function ownedArchives(Request $request): JsonResponse
  {
    $archives = (new DeleteUserAction())->ownedArchives($request->user())
      ->loadCount('records')
      ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
      ->values();

    return response()->json([
      'data' => $archives->map(fn ($archive) => [
        'uuid' => $archive->uuid,
        'name' => $archive->name,
        'records_count' => $archive->records_count,
      ]),
    ]);
  }

  public function destroy(Request $request): Response
  {
    (new DeleteUserAction())->execute(auth()->user());
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->noContent();
  }

}
