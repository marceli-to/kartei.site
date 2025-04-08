<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveUser\StoreRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\ArchiveUsersResource;
use App\Actions\User\Create as CreateUserAction;
use App\Actions\User\Update as UpdateUserAction;
use App\Actions\User\Delete as DeleteUserAction;
use App\Actions\User\Related as RelatedAction;
use App\Actions\ArchiveUser\Get as GetArchiveUserAction;
use App\Models\User;
use App\Models\Archive;

class ArchiveUserController extends Controller
{
  /**
   * Get users for a specific archive
   * 
   * @param Archive $archive
   * @return AnonymousResourceCollection
   */
  public function get(Archive $archive): AnonymousResourceCollection
  {
    return ArchiveUsersResource::collection(
      (new GetArchiveUserAction())->execute($archive, TRUE)
    );
  }

  /**
   * Get a specific user
   * 
   * @param User $user
   * @return UserResource
   */
  public function find(Request $request, User $user): UserResource
  {
    return new UserResource($user);
  }
  
  /**
   * Create a new user
   * 
   * @param StoreRequest $request
   * @return UserResource
   */
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

  /**
   * Update an existing user
   * 
   * @param StoreRequest $request
   * @param User $user
   * @return UserResource
   */
  public function update(StoreRequest $request, User $user): UserResource
  {
    (new UpdateUserAction())->execute($request->all(), $user);
    return new UserResource($user);
  }

  /**
   * Get related users
   * 
   * @return AnonymousResourceCollection
   */
  public function related(Request $request): AnonymousResourceCollection
  {
    return ArchiveUsersResource::collection(
      (new RelatedAction())->execute($request->user())
    );
  }

  /**
   * Delete a user
   * 
   * @param User $user
   * @return Response
   */
  public function destroy(Request $request, User $user): Response
  {
    (new DeleteUserAction())->execute($user);
    return response()->noContent();
  }

}
