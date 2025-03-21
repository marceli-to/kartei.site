<?php
namespace App\Actions\User;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Related
{
  public function execute(User $user)
  {
    return $user->related()->distinct()->get();
  }
}
