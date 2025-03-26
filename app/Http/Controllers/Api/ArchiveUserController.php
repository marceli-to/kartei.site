<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserRelatedResource;
use App\Actions\User\Create as CreateUserAction;
use App\Actions\User\Related as RelatedAction;

class ArchiveUserController extends Controller
{
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


}
