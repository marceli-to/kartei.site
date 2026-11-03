<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by bootstrap/app.php and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'home')->name('page.home');
Route::view('/auf-wiedersehen', 'goodbye')->name('page.goodbye');

Route::get('/archiv/{any?}', function () {
  return view('archive');
})->where('any', '.*')->middleware(['auth', 'verified'])->name('page.archive');

Route::get('/error/{any?}', function () {
  return view('archive');
})->where('any', '.*')->middleware(['auth', 'verified'])->name('page.error');

require __DIR__.'/auth.php';
