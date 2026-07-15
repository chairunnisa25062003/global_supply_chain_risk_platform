<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * ===============================================================
 * COUNTRY SERVICE
 * ===============================================================
 * Menggabungkan data dari 2 API GRATIS yang berbeda:
 *
 *   1. REST Countries API  -> data "identitas" negara
 *      (nama resmi, ibu kota, mata uang, wilayah, populasi, bendera)
 *      Tidak butuh API key.
 *
 *   2. World Bank API      -> data EKONOMI negara
 *      (GDP, inflasi)
 *      Tidak butuh API key.
 *
 * Kenapa 2 API dipisah? Karena REST Countries tidak punya data
 * GDP/inflasi, dan World Bank tidak punya data mata uang/bendera.
 * Jadi kita gabungkan hasil keduanya jadi 1 response yang lengkap.
 * ===============================================================
 */
class CountryService
{
    private string $restCountriesUrl = 'https://restcountries.com/v3.1/name';
    private string $worldBankUrl = 'https://api.worldbank.org/v2/country';

    // Kode indikator resmi dari World Bank, silakan cari kode lain
    // di https://api.worldbank.org/v2/indicator?format=json&search=...
    private const INDICATOR_GDP       = 'NY.GDP.MKTP.CD';   // GDP (current US$)
    private const INDICATOR_INFLATION = 'FP.CPI.TOTL.ZG';   // Inflasi (annual %)

    /**
     * FUNGSI UTAMA. Dipanggil dari Controller.
     * Mengembalikan 1 array lengkap: identitas negara + data ekonomi.
     */
    public function getCountryProfile(string $countryName): ?array
    {
        // Cache 6 jam per negara, supaya tidak boros request ke API luar
        // setiap kali halaman di-refresh (data GDP/populasi kan jarang berubah).
        $cacheKey = 'country_profile_' . strtolower($countryName);

        return Cache::remember($cacheKey, 21600, function () use ($countryName) {

            $basicInfo = $this->fetchBasicInfo($countryName);

            // Kalau negaranya saja tidak ketemu di REST Countries,
            // tidak ada gunanya lanjut cari data ekonominya.
            if (! $basicInfo) {
                return null;
            }

            $economicData = $this->fetchEconomicData($basicInfo['iso2']);

            return array_merge($basicInfo, $economicData);
        });
    }

    /**
     * STEP 1: Ambil data identitas negara dari REST Countries API.
     */
    private function fetchBasicInfo(string $countryName): ?array
    {
        $response = Http::get("{$this->restCountriesUrl}/{$countryName}", [
            'fields' => 'name,capital,region,subregion,population,currencies,languages,flags,cca2,cca3',
        ]);

        if (! $response->successful()) {
            return null;
        }

        // REST Countries selalu mengembalikan ARRAY (bisa beberapa hasil
        // mirip), kita ambil hasil pertama yang paling relevan.
        $data = $response->json()[0] ?? null;

        if (! $data) {
            return null;
        }

        // Data mata uang & bahasa formatnya object bersarang, kita
        // sederhanakan jadi teks biasa supaya gampang ditampilkan di blade.
        $currencyCode = array_key_first($data['currencies'] ?? []) ?? '-';
        $currencyName = $data['currencies'][$currencyCode]['name'] ?? '-';

        $languages = implode(', ', array_values($data['languages'] ?? []));

        return [
            'name'          => $data['name']['common'] ?? $countryName,
            'official_name' => $data['name']['official'] ?? $countryName,
            'capital'       => $data['capital'][0] ?? '-',
            'region'        => $data['region'] ?? '-',
            'subregion'     => $data['subregion'] ?? '-',
            'population'    => $data['population'] ?? 0,
            'currency_code' => $currencyCode,
            'currency_name' => $currencyName,
            'languages'     => $languages ?: '-',
            'flag'          => $data['flags']['png'] ?? null,
            'iso2'          => $data['cca2'] ?? null,
            'iso3'          => $data['cca3'] ?? null,
        ];
    }

    /**
     * STEP 2: Ambil GDP & Inflasi dari World Bank, pakai kode ISO2
     * yang didapat dari REST Countries di step 1.
     */
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

    /**
     * Helper generik untuk ambil 1 indikator dari World Bank.
     * `mrv=1` artinya "most recent value" -> ambil data TERBARU yang
     * tersedia saja, tidak perlu ambil histori bertahun-tahun.
     */
    private function fetchIndicator(string $iso2, string $indicatorCode, string $label): array
    {
        $response = Http::get("{$this->worldBankUrl}/{$iso2}/indicator/{$indicatorCode}", [
            'format' => 'json',
            'mrv'    => 1,
        ]);

        if (! $response->successful()) {
            return [$label => null, "{$label}_year" => null];
        }

        // Response World Bank bentuknya [metadata, [data]] — array bersarang
        $records = $response->json()[1] ?? [];
        $latest = $records[0] ?? null;

        return [
            $label => $latest['value'] ?? null,
            "{$label}_year" => $latest['date'] ?? null,
        ];
    }
}
