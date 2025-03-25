<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Actions\UserInvite\Invite;

class UserInviteController extends Controller
{
  public function invite(Request $request, User $user): JsonResponse  
  {
    (new Invite())->execute(
      $user, 
      $request->archives
    );
    return response()->noContent();
  }
}

