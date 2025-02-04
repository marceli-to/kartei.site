<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Actions\User\Create as CreateAction;

class UserController extends Controller
{
  public function index()
  {
    // get current user
    $user = Auth::user();

    $user->givePermissionTo('edit records');
    dd($user->permissions);
  }

}