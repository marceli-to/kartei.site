<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Actions\Favorite\Toggle as ToggleAction;
use App\Models\Record;

class FavoriteController extends Controller
{

  /**
   * Get the user's favorites.
   * 
   * @return 
   */
  public function get()
  {
    return response()->json(
      auth()->user()->getFavorites()
    );
  }

  /**
   * Toggle a record's favorite status.
   * 
   * @param Record $record
   * @return void
   */
  public function toggle(Record $record)
  {
    (new ToggleAction())->execute($record->id);
    return response()->noContent();
  }
}