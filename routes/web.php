<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;

Route::get('/', function () {
    return view('dashboard.index');
});

Route::get('/countries/import', [CountryController::class, 'import']);
Route::get('/countries', fn () => view('countries.index'))->name('countries');