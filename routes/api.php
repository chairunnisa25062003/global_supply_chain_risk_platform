<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\CountryController;

Route::get('/risk', [RiskController::class, 'index']);
Route::get('/countries', [CountryController::class, 'index']);