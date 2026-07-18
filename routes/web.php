<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\WatchlistController;

Route::get('/', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/watchlist', fn () => view('watchlist.index'))->name('watchlist');

    Route::get('/api/watchlist', [WatchlistController::class, 'index']);
    Route::post('/api/watchlist', [WatchlistController::class, 'store']);
    Route::delete('/api/watchlist/{id}', [WatchlistController::class, 'destroy']);
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/watchlist', fn () => view('watchlist.index'))->name('watchlist');
});

Route::get('/countries/import', [CountryController::class, 'import']);
Route::get('/countries', fn () => view('countries.index'))->name('countries');
Route::get('/weather', fn () => view('weather.index'))->name('weather');
Route::get('/currency', fn () => view('currency.index'))->name('currency');
Route::get('/ports', fn () => view('ports.index'))->name('ports');
Route::get('/news', fn () => view('news.index'))->name('news');
Route::get('/compare', fn () => view('compare.index'))->name('compare');