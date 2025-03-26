<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DummyController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ArchiveController;
use App\Http\Controllers\Api\ArchiveUserController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\UserSubscriptionController;
use App\Http\Controllers\Api\UserThemeController;
use App\Http\Controllers\Api\UserCompanyController;
use App\Http\Controllers\Api\UserPermissionController;
use App\Http\Controllers\Api\UserInviteController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
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
  Route::get('/user/profile', [UserController::class, 'find']);
  Route::put('/user/profile', [UserController::class, 'update']);
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

  // User permissions
  Route::get('/user/permissions', [UserPermissionController::class, 'get']);
  Route::put('/user/permissions/{user:uuid}', [UserPermissionController::class, 'store']);

  // User invite
  Route::post('/user/invite/{user:uuid}', [UserInviteController::class, 'invite']);

  // User routes (dynamic)
  Route::get('/user/{user:uuid}', [UserController::class, 'find']);

  // Subscription plans
  Route::get('/subscription-plans', [SubscriptionPlanController::class, 'get']);

  // Archives
  Route::get('/archives', [ArchiveController::class, 'get']);
  Route::get('/archives/admin', [ArchiveController::class, 'getByAdmin']);
  Route::get('/archives/user/{userId}', [ArchiveController::class, 'getByUser']);
  Route::get('/archives/all', [ArchiveController::class, 'getAll']);

  // Archive User
  Route::post('/archive/user', [ArchiveUserController::class, 'create']);
  Route::get('/archive/user/related', [ArchiveUserController::class, 'related']);
  Route::get('/archive/user/{uuid}', [ArchiveUserController::class, 'find']);
  Route::put('/archive/user/{uuid}', [ArchiveUserController::class, 'update']);
  Route::delete('/archive/user/{uuid}', [ArchiveUserController::class, 'destroy']);

  // Permissions and roles
  Route::get('/permissions', [PermissionController::class, 'get']);
  Route::get('/permissions/role/{role}', [PermissionController::class, 'getByRole']);
  Route::get('/permissions/user/{user}', [PermissionController::class, 'getByUser']);
  Route::get('/roles', [RoleController::class, 'get']);
  Route::get('/roles/permissions', [RoleController::class, 'getWithPermissions']);

  
});