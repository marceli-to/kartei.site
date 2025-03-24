<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
  public function get(Request $request): JsonResponse
  {
    $roles = Role::all();
    return response()->json([
      'roles' => $roles,
    ]);
  }
}