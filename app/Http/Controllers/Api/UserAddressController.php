<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddress\UpdateRequest;
use App\Http\Resources\UserAddressResource;
use App\Actions\UserAddress\Update as UpdateUserAddressAction;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
  public function address(Request $request)
  {
    return new UserAddressResource(auth()->user()->address);
  }

  public function billingAddress(Request $request)
  {
    return new UserAddressResource(auth()->user()->billing_address);
  }

  public function updateAddress(UpdateRequest $request)
  {
    $user = (new UpdateUserAddressAction())->execute(
      $request->all(), 
      auth()->user(),
      false
    );
  }
}