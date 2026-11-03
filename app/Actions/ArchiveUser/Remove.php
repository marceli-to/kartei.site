<?php
namespace App\Actions\ArchiveUser;
use App\Models\Archive;
use App\Models\User;

class Remove
{
  /**
   * Remove a user from a single archive, together with the role and
   * the per-archive permissions ("<permission>.<archive id>") for it.
   */
  public function execute(User $user, Archive $archive): void
  {
    $user->archives()->detach($archive->id);

    $user->roles()->wherePivot('archive_id', $archive->id)->detach();

    $permissions = $user->permissions
      ->filter(fn ($permission) => str_ends_with($permission->name, '.' . $archive->id));

    if ($permissions->isNotEmpty()) {
      $user->revokePermissionTo($permissions);
    }
  }
}
