<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;

Route::get('/', function () {
    return view('dashboard.index');
});

Route::get('/countries/import', [CountryController::class, 'import']);