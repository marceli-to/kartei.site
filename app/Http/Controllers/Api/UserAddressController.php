<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddress\UpdateRequest;
use App\Http\Resources\UserAddressResource;
use App\Actions\UserAddress\Update as UpdateUserAddressAction;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
  public function find(Request $request)
  {
    return new UserAddressResource(auth()->user()->address);
  }

  public function findBilling(Request $request)
  {
    return new UserAddressResource(auth()->user()->billingAddress);
  }

  public function update(UpdateRequest $request)
  {
    $user = (new UpdateUserAddressAction())->execute(
      $request->all(), 
      auth()->user(),
      false
    );
  }

  public function updateBilling(UpdateRequest $request)
  {
    $user = (new UpdateUserAddressAction())->execute(
      $request->all(), 
      auth()->user(),
      true
    );
  }
}