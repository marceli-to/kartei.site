<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Archive;
use App\Actions\UserInvite\Invite;

class UserInviteController extends Controller
{
  public function invite(Request $request, User $user): Response  
  {
    $request->validate([
      'archive' => ['required', 'string', 'exists:archives,uuid'],
    ]);

    $archive = Archive::where('uuid', $request->input('archive'))->firstOrFail();

    // Only invite users to an archive the actor administers and the user belongs to
    $this->authorize('assignToArchive', [$user, $archive]);
    abort_unless($user->isMemberOf($archive), 422, 'Der Benutzer hat keinen Zugriff auf diese Kartei.');

    (new Invite())->execute(
      $user, 
      $archive->uuid
    );
    return response()->noContent();
  }
}

