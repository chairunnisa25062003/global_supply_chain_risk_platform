<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Country;


class CountryService
{
    private string $restCountriesUrl = 'https://api.restcountries.com/countries/v5';
    private string $worldBankUrl = 'https://api.worldbank.org/v2/country';

    private const INDICATOR_GDP       = 'NY.GDP.MKTP.CD';
    private const INDICATOR_INFLATION = 'FP.CPI.TOTL.ZG';
    private const CACHE_HOURS         = 6;

    public function getCountryProfile(string $countryName): ?array
    {
        $cached = Country::where('name', $countryName)->first();

        if ($cached && $cached->updated_at->diffInHours(now()) < self::CACHE_HOURS) {
            return $this->toArray($cached);
        }

        $basicInfo = $this->fetchBasicInfo($countryName);

        if (! $basicInfo) {
            return $cached ? $this->toArray($cached) : null;
        }

        $economicData = $this->fetchEconomicData($basicInfo['iso2']);
        $fullData = array_merge($basicInfo, $economicData);

        $record = Country::updateOrCreate(
            ['name' => $fullData['name']],
            [
                'official_name'  => $fullData['official_name'],
                'capital'        => $fullData['capital'],
                'region'         => $fullData['region'],
                'subregion'      => $fullData['subregion'],
                'population'     => $fullData['population'],
                'currency_code'  => $fullData['currency_code'],
                'currency_name'  => $fullData['currency_name'],
                'languages'      => $fullData['languages'],
                'flag'           => $fullData['flag'],
                'iso2'           => $fullData['iso2'],
                'iso3'           => $fullData['iso3'],
                'gdp'            => $fullData['gdp'],
                'gdp_year'       => $fullData['gdp_year'],
                'inflation'      => $fullData['inflation'],
                'inflation_year' => $fullData['inflation_year'],
            ]
        );

        return $this->toArray($record);
    }

    public function getHistoricalIndicators(string $countryName): ?array
    {
        $basicInfo = $this->fetchBasicInfo($countryName);

        if (! $basicInfo || ! $basicInfo['iso2']) {
            $cached = Country::where('name', $countryName)->first();
            if (! $cached || ! $cached->iso2) {
                return null;
            }
            $basicInfo = ['name' => $cached->name, 'iso2' => $cached->iso2];
        }

        return [
            'name'              => $basicInfo['name'],
            'gdp_history'       => $this->fetchIndicatorHistory($basicInfo['iso2'], self::INDICATOR_GDP),
            'inflation_history' => $this->fetchIndicatorHistory($basicInfo['iso2'], self::INDICATOR_INFLATION),
        ];
    }

    private function toArray(Country $country): array
    {
        return [
            'name'          => $country->name,
            'official_name' => $country->official_name,
            'capital'       => $country->capital,
            'region'        => $country->region,
            'subregion'     => $country->subregion,
            'population'    => $country->population,
            'currency_code' => $country->currency_code,
            'currency_name' => $country->currency_name,
            'languages'     => $country->languages,
            'flag'          => $country->flag,
            'iso2'          => $country->iso2,
            'iso3'          => $country->iso3,
            'gdp'           => $country->gdp ? (float) $country->gdp : null,
            'gdp_year'      => $country->gdp_year,
            'inflation'     => $country->inflation ? (float) $country->inflation : null,
            'inflation_year'=> $country->inflation_year,
        ];
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

        $objects = $response->json('data.objects', []);

        if (empty($objects)) {
            return null;
        }

        $searchLower = strtolower(trim($countryName));

       
        $exactMatch = collect($objects)->first(function ($obj) use ($searchLower) {
            $common = strtolower($obj['names']['common'] ?? '');
            $official = strtolower($obj['names']['official'] ?? '');
            return $common === $searchLower || $official === $searchLower;
        });

        
        $partialMatch = collect($objects)->first(function ($obj) use ($searchLower) {
            return str_contains(strtolower($obj['names']['common'] ?? ''), $searchLower);
        });

        
        $data = $exactMatch ?? $partialMatch ?? $objects[0];

        return $this->parseCountryData($data, $countryName);
    }

    private function parseCountryData(array $data, string $fallbackName): array
    {
        $currency = $data['currencies'][0] ?? null;
        $currencyCode = $currency['code'] ?? '-';
        $currencyName = $currency['name'] ?? '-';

        $languages = implode(', ', array_column($data['languages'] ?? [], 'name'));

        $capitals = collect($data['capitals'] ?? []);
        $capital = $capitals->firstWhere('primary', true)['name']
            ?? $capitals->first()['name']
            ?? '-';

        return [
            'name'          => $data['names']['common'] ?? $fallbackName,
            'official_name' => $data['names']['official'] ?? $fallbackName,
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
