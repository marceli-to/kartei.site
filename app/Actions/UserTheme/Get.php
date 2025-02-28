<?php
namespace App\Actions\UserTheme;
use App\Models\User;

class Get
{
  public function execute(User $user)
  {
    return [
      'color_scheme' => $user->color_scheme,
      'color_theme' => $user->color_theme
    ];
  }
}
