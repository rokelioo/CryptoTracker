<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoCurrencyController;

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

Route::get('/', [CryptoCurrencyController::class, 'index'])->name('home');
Route::get('/search', [CryptoCurrencyController::class, 'search'])->name('search');
Route::get('/{crypto}/{timeframe?}', [CryptoCurrencyController::class, 'show'])->name('crypto');

