<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// TestController
Route::get('/user', [UserController::class, 'index']);

Route::view('/', 'home')->name('page.home');
Route::view('/auf-wiedersehen', 'goodbye')->name('page.goodbye');

Route::get('/archiv/{any?}', function () {
  return view('archive');
})->where('any', '.*')->middleware(['auth', 'verified'])->name('page.archive');

require __DIR__.'/auth.php';
