<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Actions\User\Update as UpdateUserAction;
use App\Actions\User\UpdatePassword as UpdatePasswordAction;

class UserController extends Controller
{
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

  public function destroy(User $user): JsonResponse
  {
  }
}
