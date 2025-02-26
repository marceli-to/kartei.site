<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriptionPlanResource;
use App\Actions\SubscriptionPlan\Get as GetSubscriptionPlanAction;

class SubscriptionPlanController extends Controller
{
  public function get(Request $request)
  {
    return SubscriptionPlanResource::collection(
      (new GetSubscriptionPlanAction())->execute()
    );
  }

}