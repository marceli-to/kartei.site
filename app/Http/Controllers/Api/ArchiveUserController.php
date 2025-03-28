<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveUser\StoreRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserRelatedResource;
use App\Actions\User\Create as CreateUserAction;
use App\Actions\User\Update as UpdateUserAction;
use App\Actions\User\Delete as DeleteUserAction;
use App\Actions\User\Related as RelatedAction;
use App\Models\User;

class ArchiveUserController extends Controller
{

  public function find(Request $request, User $user): UserResource
  {
    return new UserResource($user);
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

  public function update(StoreRequest $request, User $user): UserResource
  {
    (new UpdateUserAction())->execute($request->all(), $user);
    return new UserResource($user);
  }

  public function related(Request $request): AnonymousResourceCollection
  {
    return UserRelatedResource::collection(
      (new RelatedAction())->execute($request->user())
    );
  }

  public function destroy(Request $request, User $user): Response
  {
    (new DeleteUserAction())->execute($user);
    return response()->noContent();
  }

}
