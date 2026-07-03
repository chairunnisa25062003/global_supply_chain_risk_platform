<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Country;

class CountryService
{
    public function importCountries()
    {
        $response = Http::get('https://restcountries.com/v3.1/all');

        if (!$response->successful()) {
            return false;
        }

        $countries = $response->json();

        foreach ($countries as $item) {

            $currencies = $item['currencies'] ?? [];

            $currencyCode = '';
            $currencyName = '';

            if (!empty($currencies)) {
                $currencyCode = array_key_first($currencies);
                $currencyName = $currencies[$currencyCode]['name'] ?? '';
            }

            Country::updateOrCreate(
                [
                    'country_code' => $item['cca2'] ?? '',
                ],
                [
                    'country_name' => $item['name']['common'] ?? '',
                    'capital' => $item['capital'][0] ?? '',
                    'region' => $item['region'] ?? '',
                    'currency_code' => $currencyCode,
                    'currency_name' => $currencyName,
                    'latitude' => $item['latlng'][0] ?? null,
                    'longitude' => $item['latlng'][1] ?? null,
                    'flag' => $item['flags']['png'] ?? '',
                ]
            );
        }

        return true;
    }
}