<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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

    public function getHistoricalIndicators(string $countryName): ?array
    {
        $cacheKey = 'country_history_' . strtolower($countryName);

        return Cache::remember($cacheKey, 21600, function () use ($countryName) {

            $basicInfo = $this->fetchBasicInfo($countryName);

            if (! $basicInfo || ! $basicInfo['iso2']) {
                return null;
            }

            return [
                'name'              => $basicInfo['name'],
                'gdp_history'       => $this->fetchIndicatorHistory($basicInfo['iso2'], self::INDICATOR_GDP),
                'inflation_history' => $this->fetchIndicatorHistory($basicInfo['iso2'], self::INDICATOR_INFLATION),
            ];
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

    private function fetchIndicatorHistory(string $iso2, string $indicatorCode): array
    {
        $currentYear = (int) date('Y');
        $startYear = $currentYear - 15;

        $response = Http::get("{$this->worldBankUrl}/{$iso2}/indicator/{$indicatorCode}", [
            'format' => 'json',
            'date'   => "{$startYear}:{$currentYear}",
            'per_page' => 100,
        ]);

        if (! $response->successful()) {
            return [];
        }

        $records = $response->json()[1] ?? [];

        return collect($records)
            ->filter(fn ($r) => $r['value'] !== null)
            ->map(fn ($r) => ['year' => $r['date'], 'value' => $r['value']])
            ->sortBy('year')
            ->values()
            ->toArray();
    }
}
