<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class CurrencyService
{
    private string $exchangeRateApiUrl = 'https://open.er-api.com/v6/latest';
    private string $frankfurterUrl = 'https://api.frankfurter.dev/v1';

    public function getCurrencyData(string $base, string $target): ?array
    {
        $cacheKey = "currency_{$base}_{$target}";

        return Cache::remember($cacheKey, 3600, function () use ($base, $target) {

            $latest = $this->fetchLatestRate($base, $target);

            if (! $latest) {
                return null;
            }

            $history = $this->fetchHistoricalSeries($base, $target);

            return [
                'base'    => $base,
                'target'  => $target,
                'rate'    => $latest['rate'],
                'date'    => $latest['date'],
                'history' => $history,
            ];
        });
    }

    
    private function fetchLatestRate(string $base, string $target): ?array
    {
        $response = Http::get("{$this->exchangeRateApiUrl}/{$base}");

        if (! $response->successful() || $response->json('result') !== 'success') {
            
            return $this->fetchLatestRateFallback($base, $target);
        }

        $rate = $response->json("rates.{$target}");

        if ($rate === null) {
            return $this->fetchLatestRateFallback($base, $target);
        }

       
        $rawDate = $response->json('time_last_update_utc');
        $date = $rawDate ? Carbon::parse($rawDate)->format('Y-m-d') : now()->format('Y-m-d');

        return ['rate' => $rate, 'date' => $date];
    }

    
    private function fetchLatestRateFallback(string $base, string $target): ?array
    {
        $response = Http::get("{$this->frankfurterUrl}/latest", [
            'base'    => $base,
            'symbols' => $target,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $rate = $response->json("rates.{$target}");

        if ($rate === null) {
            return null;
        }

        return ['rate' => $rate, 'date' => $response->json('date')];
    }

    
    private function fetchHistoricalSeries(string $base, string $target): array
    {
        $endDate = Carbon::now()->format('Y-m-d');
        $startDate = Carbon::now()->subDays(30)->format('Y-m-d');

        $response = Http::get("{$this->frankfurterUrl}/{$startDate}..{$endDate}", [
            'base'    => $base,
            'symbols' => $target,
        ]);

        if (! $response->successful()) {
            return [];
        }

        $rates = $response->json('rates', []);

        $series = [];
        foreach ($rates as $date => $values) {
            $series[] = [
                'date' => $date,
                'rate' => $values[$target] ?? null,
            ];
        }

        usort($series, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return $series;
    }
}
