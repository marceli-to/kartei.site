<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Actions\User\Update as UpdateUserAction;

class UserController extends Controller
{
  public function update(UserUpdateRequest $request): JsonResponse
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

  public function destroy(string $id): JsonResponse
  {
  }
}
