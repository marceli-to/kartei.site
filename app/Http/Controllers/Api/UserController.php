<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserRelatedResource;
use App\Http\Resources\UserPermissionResource;
use App\Actions\User\Update as UpdateUserAction;
use App\Actions\User\UpdatePassword as UpdatePasswordAction;
use App\Actions\User\Create as CreateUserAction;
use App\Actions\User\Delete as DeleteUserAction;
use App\Actions\User\Related as RelatedAction;
use App\Actions\ArchiveUser\Attach as AttachArchiveUserAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function find(Request $request): UserResource
  {
    return new UserResource($request->user());
  }

  public function create(StoreRequest $request): UserResource
  {
    return new UserResource(
      (new CreateUserAction())->execute(
        array_merge(
          ['password' => config('user.welcome_password')],
          $request->all()
        )
      )
    );
  }

  public function related(Request $request): AnonymousResourceCollection
  {
    return UserRelatedResource::collection(
      (new RelatedAction())->execute($request->user())
    );
  }

  public function permissions(Request $request): UserPermissionResource
  {
    return new UserPermissionResource($request->user());
  }

  public function update(UpdateRequest $request): JsonResponse
  {
    $user = (new UpdateUserAction())->execute(
      $request->all(), 
      auth()->user()
    );

    return response()->json([
      'user' => $user,
      'email_changed' => $user->wasChanged('email')
    ]);
  }

  public function password(UpdatePasswordRequest $request): JsonResponse
  {
    (new UpdatePasswordAction())->execute(
      $request->all(), 
      auth()->user()
    );
    return response()->noContent();
  }

  public function destroy(Request $request)
  {
    (new DeleteUserAction())->execute(auth()->user());
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->noContent();
  }
}
