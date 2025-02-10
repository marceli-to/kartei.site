<?php
namespace App\Actions\Archive;
use App\Models\Archive;

class Get
{
  public function execute()
  {
    $user = auth()->user();
    $query = Archive::query();
    if (!$user->hasRole('Super Admin')) {
      $query->whereHas('users', function($q) use ($user) {
        $q->where('user_id', $user->id);
      });
    }
    return $query->get();
  }
}
