<?php

use Illuminate\Support\Facades\Route;

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


Route::view('/', 'home')->name('page.home');

Route::get('/archiv/{any?}', function () {
  return view('archive');
})->where('any', '.*')->middleware(['auth', 'verified'])->name('page.archive');

require __DIR__.'/auth.php';
