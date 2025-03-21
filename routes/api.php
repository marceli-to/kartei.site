<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DummyController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ArchiveController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\UserSubscriptionController;
use App\Http\Controllers\Api\UserThemeController;
use App\Http\Controllers\Api\UserCompanyController;
use App\Http\Controllers\Api\SubscriptionPlanController;

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


Route::middleware('auth:sanctum')->group(function () {

  // Uploads
  Route::post('/upload', [UploadController::class, 'store']);
  Route::delete('/upload/temp/{name}', [UploadController::class, 'destroyTemp']);
  Route::delete('/upload/{uuid}', [UploadController::class, 'destroy']);

  // Users
  Route::get('/users', [UserController::class, 'get']);
  Route::get('/user/related', [UserController::class, 'related']);
  Route::get('/user/profile', [UserController::class, 'find']);
  Route::put('/user/profile', [UserController::class, 'update']);
  Route::get('/user/permissions', [UserController::class, 'permissions']);
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

  // User theme
  Route::get('/user/theme', [UserThemeController::class, 'find']);
  Route::put('/user/theme', [UserThemeController::class, 'update']);

  // User companies
  Route::get('/user/companies', [UserCompanyController::class, 'get']);
  Route::post('/user/company', [UserCompanyController::class, 'create']);
  Route::put('/user/company/{company:uuid}', [UserCompanyController::class, 'update']);
  Route::delete('/user/company/{company:uuid}', [UserCompanyController::class, 'destroy']);
  
  // Subscription plans
  Route::get('/subscription-plans', [SubscriptionPlanController::class, 'get']);

  // Archives
  Route::get('/archives', [ArchiveController::class, 'get']);
  Route::get('/archives/admin', [ArchiveController::class, 'getByAdmin']);
  Route::get('/archives/user/{userId}', [ArchiveController::class, 'getByUser']);
  Route::get('/archives/all', [ArchiveController::class, 'getAll']);
  
});