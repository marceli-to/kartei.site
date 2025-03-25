<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Actions\Role\Get as GetRolesAction;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RolePermissionResource;

class RoleController extends Controller
{
  public function get(Request $request): AnonymousResourceCollection
  {
    $roles = (new GetRolesAction())->execute(
      [
        'exclude' => ['Super Admin', 'Admin'],
      ]
    );

    return RoleResource::collection($roles); 
  }

  public function getWithPermissions(Request $request): JsonResponse
  {
    $roles = (new GetRolesAction())->execute(
      [
        'exclude' => ['Super Admin', 'Admin'],
        'with_permissions' => true
      ]
    );
    return response()->json($roles);
  }
}