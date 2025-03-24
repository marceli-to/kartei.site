<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
  public function get(Request $request): JsonResponse
  {
    $permissions = Permission::all();
    return response()->json([
      'permissions' => $permissions,
    ]);
  }
}