<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CountryService
{
    public function getCountries()
    {
        $response = Http::get('https://restcountries.com/v3.1/all');

        return $response->json();
    }
}