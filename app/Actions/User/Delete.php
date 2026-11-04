<?php
namespace App\Actions\User;
use App\Actions\Archive\Delete as DeleteArchiveAction;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Delete
{
  public function execute(User $user): bool
  {
    return DB::transaction(function () use ($user) {
      // Archives the user owns go with the account (records, images and all)
      foreach ($this->ownedArchives($user) as $archive) {
        (new DeleteArchiveAction())->execute($archive);
      }

      // remove roles
      $user->roles()->detach();

      // remove permissions
      $user->permissions()->detach();

      // remove archives
      $user->archives()->detach();
      $user->flushArchiveMemberships();

      return $user->delete();
    });
  }

  /**
   * Archives this user is the admin of (attached without a pivot role, see
   * User::isAdminOf). An archive that has another admin is kept.
   */
  public function ownedArchives(User $user): Collection
  {
    if (!$user->hasRole('Admin')) {
      return new Collection();
    }

    return $user->archives()
      ->wherePivotNull('role_id')
      ->get()
      ->reject(fn (Archive $archive) => DB::table('archive_user')
        ->where('archive_id', $archive->id)
        ->where('user_id', '!=', $user->id)
        ->whereNull('role_id')
        ->exists());
  }
}
