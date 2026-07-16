<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * ===============================================================
 * COUNTRY SERVICE
 * ===============================================================
 * CATATAN PENTING: REST Countries API pernah GRATIS TANPA KEY
 * (versi v3.1), tapi per pertengahan 2026 mereka pindah ke versi
 * BARU (v5) yang WAJIB pakai API key (gratis 500 request/bulan,
 * tanpa kartu kredit). Endpoint lama (v3.1) sudah tidak aktif.
 *
 * Kalau ditanya dosen kenapa API ini butuh key sementara yang lain
 * (Open-Meteo, GNews sisi sentiment, dst) beda-beda kebijakannya,
 * jawaban jujurnya: itu keputusan bisnis masing-masing penyedia
 * API, di luar kendali kita sebagai pengguna API gratis.
 *
 * Menggabungkan data dari 2 sumber:
 *   1. REST Countries API v5 -> identitas negara (butuh API key)
 *   2. World Bank API        -> data ekonomi (masih gratis tanpa key)
 * ===============================================================
 */
class CountryService
{
    private string $restCountriesUrl = 'https://api.restcountries.com/countries/v5';
    private string $worldBankUrl = 'https://api.worldbank.org/v2/country';

    private const INDICATOR_GDP       = 'NY.GDP.MKTP.CD';
    private const INDICATOR_INFLATION = 'FP.CPI.TOTL.ZG';

    public function getCountryProfile(string $countryName): ?array
    {
        $cacheKey = 'country_profile_' . strtolower($countryName);

        return Cache::remember($cacheKey, 21600, function () use ($countryName) {

            $basicInfo = $this->fetchBasicInfo($countryName);

            if (! $basicInfo) {
                return null;
            }

            $economicData = $this->fetchEconomicData($basicInfo['iso2']);

            return array_merge($basicInfo, $economicData);
        });
    }

    

    private function fetchBasicInfo(string $countryName): ?array
    {
        $apiKey = config('services.restcountries.key');

        if (empty($apiKey)) {
           
        
            return null;
        }

        $response = Http::withToken($apiKey)->get($this->restCountriesUrl, [
            'q'               => $countryName,
            'response_fields' => 'names,capitals,region,subregion,population,currencies,languages,flag,codes',
        ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json('data.objects.0');

        if (! $data) {
            return null;
        }

    
        $currency = $data['currencies'][0] ?? null;
        $currencyCode = $currency['code'] ?? '-';
        $currencyName = $currency['name'] ?? '-';

     
        $languages = implode(', ', array_column($data['languages'] ?? [], 'name'));


        $capitals = collect($data['capitals'] ?? []);
        $capital = $capitals->firstWhere('primary', true)['name']
            ?? $capitals->first()['name']
            ?? '-';

        return [
            'name'          => $data['names']['common'] ?? $countryName,
            'official_name' => $data['names']['official'] ?? $countryName,
            'capital'       => $capital,
            'region'        => $data['region'] ?? '-',
            'subregion'     => $data['subregion'] ?? '-',
            'population'    => $data['population'] ?? 0,
            'currency_code' => $currencyCode,
            'currency_name' => $currencyName,
            'languages'     => $languages ?: '-',
            'flag'          => $data['flag']['url_png'] ?? null,
            'iso2'          => $data['codes']['alpha_2'] ?? null,
            'iso3'          => $data['codes']['alpha_3'] ?? null,
        ];
    }

    private function fetchEconomicData(?string $iso2): array
    {
        if (! $iso2) {
            return ['gdp' => null, 'gdp_year' => null, 'inflation' => null, 'inflation_year' => null];
        }

        return [
            ...$this->fetchIndicator($iso2, self::INDICATOR_GDP, 'gdp'),
            ...$this->fetchIndicator($iso2, self::INDICATOR_INFLATION, 'inflation'),
        ];
    }

    private function fetchIndicator(string $iso2, string $indicatorCode, string $label): array
    {
        $response = Http::get("{$this->worldBankUrl}/{$iso2}/indicator/{$indicatorCode}", [
            'format' => 'json',
            'mrv'    => 1,
        ]);

        if (! $response->successful()) {
            return [$label => null, "{$label}_year" => null];
        }

        $records = $response->json()[1] ?? [];
        $latest = $records[0] ?? null;

        return [
            $label => $latest['value'] ?? null,
            "{$label}_year" => $latest['date'] ?? null,
        ];
    }
}
