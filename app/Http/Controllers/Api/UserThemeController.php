<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserTheme\UpdateRequest;
use App\Http\Resources\UserThemeResource;
use App\Actions\UserTheme\Get as GetUserThemeAction;
use App\Actions\UserTheme\Update as UpdateUserThemeAction;

class UserThemeController extends Controller
{
  public function find(Request $request)
  {
    $themes = (new GetUserThemeAction())->execute(auth()->user());
    if (!$themes) {
      return response()->noContent();
    }
    return new UserThemeResource($themes);
  }

  public function update(UpdateRequest $request)
  {
    (new UpdateUserThemeAction())->execute(
      $request->all(),
      auth()->user()
    );
    return response()->noContent();
  }
}