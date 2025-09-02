<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Http\Requests\UserAddress\UpdateRequest;
use App\Http\Resources\UserAddressResource;
use App\Actions\UserAddress\Update as UpdateUserAddressAction;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
  public function find(Request $request): UserAddressResource
  {
    if (auth()->user()->address) {
      $this->authorize('view', auth()->user()->address);
    }
    
    return new UserAddressResource(auth()->user()->address);
  }

  public function findBilling(Request $request): UserAddressResource
  {
    if (auth()->user()->billingAddress) {
      $this->authorize('view', auth()->user()->billingAddress);
    }

    return new UserAddressResource(auth()->user()->billingAddress);
  }

  public function update(UpdateRequest $request): UserAddressResource
  {
    auth()->user()->address ? 
      $this->authorize('update', auth()->user()->address) : 
      $this->authorize('create', Address::class);

    $user = (new UpdateUserAddressAction())->execute(
      $request->all(), 
      auth()->user(),
      false
    );

    return new UserAddressResource($user->address);
  }

  public function updateBilling(UpdateRequest $request): UserAddressResource
  {
    auth()->user()->address ? 
      $this->authorize('update', auth()->user()->address) : 
      $this->authorize('create', Address::class);

    $user = (new UpdateUserAddressAction())->execute(
      $request->all(), 
      auth()->user(),
      true
    );

    return new UserAddressResource($user->address);
  }
}