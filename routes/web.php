<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\WatchlistController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PortController as AdminPortController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;

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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::post('/users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/ports', [AdminPortController::class, 'index'])->name('ports');
    Route::post('/ports', [AdminPortController::class, 'store'])->name('ports.store');
    Route::delete('/ports/{port}', [AdminPortController::class, 'destroy'])->name('ports.destroy');

    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
});

Route::get('/countries/import', [CountryController::class, 'import']);
Route::get('/countries', fn () => view('countries.index'))->name('countries');
Route::get('/weather', fn () => view('weather.index'))->name('weather');
Route::get('/currency', fn () => view('currency.index'))->name('currency');
Route::get('/ports', fn () => view('ports.index'))->name('ports');
Route::get('/news', fn () => view('news.index'))->name('news');
Route::get('/compare', fn () => view('compare.index'))->name('compare');
Route::get('/economy', fn () => view('economy.index'))->name('economy');
Route::get('/risk', fn () => view('risk.index'))->name('risk');
Route::get('/analytics', fn () => view('analytics.index'))->name('analytics');