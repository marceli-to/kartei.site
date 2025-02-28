<?php
namespace App\Actions\UserTheme;
use App\Models\UserSubscription;
use App\Models\User;

class Update
{
  public function execute(array $data, User $user): User
  {
    $user->color_scheme = $data['color_scheme'];
    $user->color_theme = $data['color_theme'];
    $user->save();
    return $user;
  }
}