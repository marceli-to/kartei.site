<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserSubscription\UpdateRequest;
use App\Http\Resources\UserSubscriptionResource;
use App\Actions\UserSubscription\Get as GetUserSubscriptionAction;
use App\Actions\UserSubscription\Update as UpdateUserSubscriptionAction;

class UserSubscriptionController extends Controller
{
  public function find(Request $request)
  {
    $subscription = (new GetUserSubscriptionAction())->execute(auth()->user());
    return new UserSubscriptionResource($subscription);
  }

  public function update(UpdateRequest $request)
  {
    (new UpdateUserSubscriptionAction())->execute(
      $request->all(),
      auth()->user()
    );
    return response()->noContent();
  }
}