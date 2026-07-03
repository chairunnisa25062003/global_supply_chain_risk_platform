<?php

namespace App\Http\Controllers;

use App\Services\CountryService;

class CountryController extends Controller
{
    public function import(CountryService $countryService)
    {
        $countryService->importCountries();

        return "Country imported successfully";
    }
}