<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;

Route::get('/', function () {
    return view('dashboard.index');
});

Route::get('/countries/import', [CountryController::class, 'import']);
Route::get('/countries', fn () => view('countries.index'))->name('countries');
Route::get('/weather', fn () => view('weather.index'))->name('weather');
Route::get('/currency', fn () => view('currency.index'))->name('currency');
Route::get('/ports', fn () => view('ports.index'))->name('ports');
Route::get('/news', fn () => view('news.index'))->name('news');
Route::get('/compare', fn () => view('compare.index'))->name('compare');