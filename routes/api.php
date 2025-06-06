<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DummyController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ArchiveController;
use App\Http\Controllers\Api\ArchiveUserController;
use App\Http\Controllers\Api\ArchiveSettingsController;
use App\Http\Controllers\Api\ArchiveMetaController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RecordController;
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
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\FavoriteController;

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
  Route::delete('/upload/{url}', [UploadController::class, 'destroy'])->where('url', '.*');;

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
  Route::get('/user/permissions/{user:uuid}', [UserPermissionController::class, 'getByUser']);
  Route::put('/user/permissions/store/{user:uuid}', [UserPermissionController::class, 'store']);
  Route::put('/user/permissions/update/{user:uuid}', [UserPermissionController::class, 'update']);

  // User invite
  Route::post('/user/invite/{user:uuid}', [UserInviteController::class, 'invite']);

  // User routes (dynamic)
  Route::get('/user/{user:uuid}', [UserController::class, 'find']);

  // Subscription plans
  Route::get('/subscription-plans', [SubscriptionPlanController::class, 'get']);

  // Archive meta
  Route::get('/archive/meta/{archive:uuid}', [ArchiveMetaController::class, 'get']);

  // Archive settings
  Route::get('/archive/settings/{archive:uuid}', [ArchiveSettingsController::class, 'get']);
  Route::put('/archive/settings/{archive:uuid}', [ArchiveSettingsController::class, 'store']);
  Route::delete('/archive/settings/field/{uuid}/{archive:uuid}', [ArchiveSettingsController::class, 'destroy']);

  // Archives
  Route::get('/archives', [ArchiveController::class, 'get']);
  Route::get('/archives/admin', [ArchiveController::class, 'getByAdmin']);
  Route::get('/archives/user/{user:uuid}', [ArchiveController::class, 'getByUser']);
  Route::get('/archives/all', [ArchiveController::class, 'getAll']);
  Route::get('/archive/{archive:uuid}', [ArchiveController::class, 'find']);
  Route::post('/archive', [ArchiveController::class, 'create']);
  Route::put('/archive/{archive:uuid}', [ArchiveController::class, 'update']);
  Route::delete('/archive/{archive:uuid}', [ArchiveController::class, 'destroy']);
  
  // Archive User
  Route::post('/archive/user', [ArchiveUserController::class, 'create']);
  Route::get('/archive/users/related', [ArchiveUserController::class, 'related']);
  Route::get('/archive/users/{archive:uuid}', [ArchiveUserController::class, 'get']);
  Route::get('/archive/user/{user:uuid}', [ArchiveUserController::class, 'find']);
  Route::put('/archive/user/{user:uuid}', [ArchiveUserController::class, 'update']);
  Route::delete('/archive/user/{user:uuid}', [ArchiveUserController::class, 'destroy']);

  // Category
  Route::get('/categories/{archive:uuid}', [CategoryController::class, 'get']);
  Route::put('/category/{archive:uuid}', [CategoryController::class, 'store']);
    
  // Record
  Route::get('/records/{archive:uuid}', [RecordController::class, 'get']);
  Route::get('/record/{record:uuid}', [RecordController::class, 'find']);
  Route::post('/record', [RecordController::class, 'create']);
  Route::put('/record/{record:uuid}', [RecordController::class, 'update']);
  Route::delete('/record/{record:uuid}', [RecordController::class, 'destroy']);

  // Tags
  Route::get('/tags/{archive:uuid}', [TagController::class, 'get']);
  Route::put('/tags/{archive:uuid}', [TagController::class, 'store']);
  Route::delete('/tag/{tag:uuid}', [TagController::class, 'destroy']);  

  // Permissions and roles
  Route::get('/permissions', [PermissionController::class, 'get']);
  Route::get('/permissions/role/{role}', [PermissionController::class, 'getByRole']);
  Route::get('/permissions/user/{user:uuid}', [PermissionController::class, 'getByUser']);
  Route::get('/roles', [RoleController::class, 'get']);
  Route::get('/roles/permissions', [RoleController::class, 'getWithPermissions']);

  // Favorite
  Route::get('/favorites', [FavoriteController::class, 'get']);
  Route::get('/favorite/toggle/{record:uuid}', [FavoriteController::class, 'toggle']);
});