<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DummyController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ArchiveController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\UserSubscriptionController;
use App\Http\Controllers\Api\SubscriptionPlanController;

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
  // Uploads
  Route::post('/upload', [UploadController::class, 'store']);
  Route::delete('/upload/temp/{name}', [UploadController::class, 'destroyTemp']);
  Route::delete('/upload/{uuid}', [UploadController::class, 'destroy']);

  // Users
  Route::get('/users', [UserController::class, 'get']);
  Route::put('/user', [UserController::class, 'update']);
  Route::post('/user/password', [UserController::class, 'password']);
  Route::delete('/user', [UserController::class, 'destroy']);

  // User addresses
  Route::get('/user/address', [UserAddressController::class, 'find']);
  Route::put('/user/address', [UserAddressController::class, 'update']);
  Route::get('/user/billing-address', [UserAddressController::class, 'findBilling']);
  Route::put('/user/billing-address', [UserAddressController::class, 'updateBilling']);

  // User subscriptions
  Route::get('/user/subscription', [UserSubscriptionController::class, 'find']);
  Route::put('/user/subscription', [UserSubscriptionController::class, 'update']);

  // Subscription Plans
  Route::get('/subscription-plans', [SubscriptionPlanController::class, 'get']);

  // Archives
  Route::get('/archives', [ArchiveController::class, 'get']);
});