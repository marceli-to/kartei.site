<?php
namespace App\Policies;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArchivePolicy
{
  use HandlesAuthorization;

  public function edit(User $user, Archive $archive)
  {
    return $user->can('edit') && $user->hasPermissionTo('archive.edit.' . $archive->id);
  }

  public function delete(User $user, Archive $archive)
  {
    return $user->can('delete') && $user->hasPermissionTo('archive.delete.' . $archive->id);
  }
}