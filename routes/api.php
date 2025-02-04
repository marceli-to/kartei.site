<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\DummyController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\ArchiveController;

use App\Http\Resources\UserResource;

use App\Models\Archive;
use App\Models\Record;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return new UserResource($request->user());
});

Route::middleware('auth:sanctum')->get('/permissions/{resourceType}/{resourceId}', function (Request $request, $resourceType, $resourceId) {
  $class = match($resourceType) {
    'archive' => Archive::class,
    'record' => Record::class,
    default => abort(404)
  };
  $resource = $class::findOrFail($resourceId);
  return [
    'permissions' => $request->user()->getAllPermissions()
      ->filter(fn($permission) => Str::endsWith($permission->name, ".{$resource->id}"))
      //->map(fn($p) => Str::beforeLast($p->name, '.'))
      ->unique()
      ->values()
  ];
});

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/upload', [FileUploadController::class, 'store']);
  Route::get('/archives', [ArchiveController::class, 'get']);
});