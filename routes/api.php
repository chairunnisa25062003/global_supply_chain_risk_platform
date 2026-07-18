<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ComparisonController;
use App\Http\Controllers\Api\EconomyController;

Route::get('/risk', [RiskController::class, 'index']);
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/weather', [WeatherController::class, 'index']);
Route::get('/currency', [CurrencyController::class, 'index']);
Route::get('/ports', [PortController::class, 'index']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/compare', [ComparisonController::class, 'index']);
Route::get('/economy', [EconomyController::class, 'index']);