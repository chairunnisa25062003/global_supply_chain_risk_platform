<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WeatherCache;

class WeatherService
{
    private string $geocodingUrl = 'https://geocoding-api.open-meteo.com/v1/search';
    private string $forecastUrl  = 'https://api.open-meteo.com/v1/forecast';
    private const CACHE_MINUTES = 15;

    // How many times to retry a request that fails transiently (timeouts,
    // connection errors, and 429 rate limits), with an increasing delay
    // between attempts (in milliseconds).
    private const RETRY_TIMES = 3;
    private const RETRY_DELAY_MS = 1000;

    // If the cache is stale (older than CACHE_MINUTES) but the API is down
    // or rate-limited, we still prefer showing "last known" data over
    // null/Unknown, as long as it isn't ancient.
    private const STALE_CACHE_MAX_MINUTES = 6 * 60; // 6 hours

    public function getWeatherByLocationName(string $locationName): ?array
    {
        $cached = WeatherCache::where('location_name', $locationName)->first();

        // Only trust the cache as a true cache hit if it's fresh AND has real data.
        if ($cached && $this->isCacheValid($cached)) {
            return array_merge($this->toArray($cached), [
                'country' => $cached->country ?? '-',
            ]);
        }

        $location = $this->geocode($locationName);

        if (! $location) {
            Log::warning('Weather geocode failed, falling back to cache if available', [
                'location_name' => $locationName,
            ]);

            return $this->staleCacheFallback($cached);
        }

        $weather = $this->fetchCurrentWeather($location['latitude'], $location['longitude']);
        $fullData = array_merge($location, $weather);

        // Don't overwrite a good cache with a failed fetch — reuse the stale
        // cache (even beyond the normal TTL, up to STALE_CACHE_MAX_MINUTES)
        // if this attempt came back empty, so the UI doesn't regress from
        // "last known values" to "null / Unknown" every time the API rate-limits us.
        if ($weather['temperature'] === null) {
            Log::warning('Forecast fetch failed, attempting stale-cache fallback', [
                'location_name' => $locationName,
            ]);

            $fallback = $this->staleCacheFallback($cached);

            if ($fallback) {
                return array_merge($fallback, [
                    'country' => $location['country'] ?? $fallback['country'],
                ]);
            }

            // No usable cache at all — return what we have (nulls) rather than nothing,
            // so the frontend at least gets the location name/country instead of a 404.
            return [
                'location_name' => $location['location_name'],
                'country'       => $location['country'],
                'latitude'      => $fullData['latitude'],
                'longitude'     => $fullData['longitude'],
                'temperature'   => $fullData['temperature'],
                'condition'     => $fullData['condition'],
                'precipitation' => $fullData['precipitation'],
                'wind_speed'    => $fullData['wind_speed'],
                'is_storm'      => $fullData['is_storm'],
            ];
        }

        $record = WeatherCache::updateOrCreate(
            ['location_name' => $locationName],
            [
                'country'       => $location['country'],
                'latitude'      => $fullData['latitude'],
                'longitude'     => $fullData['longitude'],
                'temperature'   => $fullData['temperature'],
                'condition'     => $fullData['condition'],
                'precipitation' => $fullData['precipitation'],
                'wind_speed'    => $fullData['wind_speed'],
                'is_storm'      => $fullData['is_storm'],
                'fetched_at'    => now(),
            ]
        );

        return array_merge($this->toArray($record), [
            'location_name' => $location['location_name'],
            'country'       => $location['country'],
        ]);
    }

    /**
     * Cache is only valid as a true hit if it's fresh AND has real (non-null) data.
     * Prevents a failed fetch from being treated as a valid 15-minute cache hit.
     */
    private function isCacheValid(WeatherCache $cache): bool
    {
        $isFresh = $cache->fetched_at->diffInMinutes(now()) < self::CACHE_MINUTES;
        $hasData = $cache->temperature !== null;

        return $isFresh && $hasData;
    }

    /**
     * When the live API fails (rate limited, down, network error), fall back
     * to the last known good cache even if it's beyond the normal TTL —
     * as long as it isn't older than STALE_CACHE_MAX_MINUTES and has real data.
     * Returns null if there's nothing usable to fall back to.
     */
    private function staleCacheFallback(?WeatherCache $cached): ?array
    {
        if (! $cached || $cached->temperature === null) {
            return null;
        }

        if ($cached->fetched_at->diffInMinutes(now()) > self::STALE_CACHE_MAX_MINUTES) {
            return null;
        }

        return $this->toArray($cached);
    }

    private function toArray(WeatherCache $cache): array
    {
        return [
            'location_name' => $cache->location_name,
            'country'       => $cache->country ?? '-',
            'latitude'      => $cache->latitude,
            'longitude'     => $cache->longitude,
            'temperature'   => $cache->temperature,
            'condition'     => $cache->condition,
            'precipitation' => $cache->precipitation,
            'wind_speed'    => $cache->wind_speed,
            'is_storm'      => $cache->is_storm,
        ];
    }

    private function geocode(string $name): ?array
    {
        $response = Http::retry(self::RETRY_TIMES, self::RETRY_DELAY_MS, function ($exception, $request) {
            // Retry on connection errors, and on 429 (rate limit) / 5xx responses.
            if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                return true;
            }

            if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                $status = $exception->response->status();

                return $status === 429 || $status >= 500;
            }

            return false;
        }, throw: false)->get($this->geocodingUrl, [
            'name'     => $name,
            'count'    => 1,
            'language' => 'en',
            'format'   => 'json',
        ]);

        if (! $response->successful()) {
            Log::warning('Open-Meteo geocoding request failed', [
                'location_name' => $name,
                'status'        => $response->status(),
                'body'          => $response->body(),
            ]);

            return null;
        }

        $result = $response->json('results')[0] ?? null;

        if (! $result) {
            Log::info('Open-Meteo geocoding returned no results', [
                'location_name' => $name,
            ]);

            return null;
        }

        return [
            'location_name' => $result['name'],
            'country'       => $result['country'] ?? '-',
            'latitude'      => $result['latitude'],
            'longitude'     => $result['longitude'],
        ];
    }

    private function fetchCurrentWeather(float $lat, float $lon): array
    {
        $response = Http::retry(self::RETRY_TIMES, self::RETRY_DELAY_MS, function ($exception, $request) {
            if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                return true;
            }

            if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                $status = $exception->response->status();

                return $status === 429 || $status >= 500;
            }

            return false;
        }, throw: false)->get($this->forecastUrl, [
            'latitude'  => $lat,
            'longitude' => $lon,
            'current'   => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
            'timezone'  => 'auto',
        ]);

        if (! $response->successful()) {
            Log::warning('Open-Meteo forecast request failed', [
                'latitude'  => $lat,
                'longitude' => $lon,
                'status'    => $response->status(),
                'body'      => $response->body(),
            ]);

            return [
                'temperature'   => null,
                'precipitation' => null,
                'wind_speed'    => null,
                'condition'     => 'Unknown',
                'is_storm'      => false,
            ];
        }

        $current = $response->json('current', []);
        $code = $current['weather_code'] ?? null;

        return [
            'temperature'    => $current['temperature_2m'] ?? null,
            'precipitation'  => $current['precipitation'] ?? null,
            'wind_speed'     => $current['wind_speed_10m'] ?? null,
            'condition'      => $this->weatherCodeToLabel($code),
            'is_storm'       => $this->isStormCondition($code, $current['wind_speed_10m'] ?? 0),
        ];
    }

    private function weatherCodeToLabel(?int $code): string
    {
        return match (true) {
            $code === null       => 'Unknown',
            $code === 0          => 'Clear Sky',
            in_array($code, [1, 2, 3]) => 'Partly Cloudy',
            in_array($code, [45, 48])  => 'Fog',
            in_array($code, [51, 53, 55, 56, 57]) => 'Drizzle',
            in_array($code, [61, 63, 65, 66, 67]) => 'Rain',
            in_array($code, [71, 73, 75, 77])     => 'Snow',
            in_array($code, [80, 81, 82])         => 'Rain Showers',
            in_array($code, [95, 96, 99])         => 'Thunderstorm',
            default => 'Unknown',
        };
    }

    private function isStormCondition(?int $code, float $windSpeed): bool
    {
        $isThunderstorm = in_array($code, [95, 96, 99], true);
        $isStrongWind = $windSpeed >= 50;

        return $isThunderstorm || $isStrongWind;
    }
}
