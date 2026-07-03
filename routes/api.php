<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RiskController;

Route::get('/risk', [RiskController::class, 'index']);