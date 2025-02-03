<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DummyController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Resources\UserResource;

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

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/upload', [FileUploadController::class, 'store']);
  // Route::get('/participants', [ParticipantController::class, 'index']);
  // Route::put('/participant/{participant}', [ParticipantController::class, 'update']);
  // Route::delete('/participant/{participant}', [ParticipantController::class, 'destroy']);
});