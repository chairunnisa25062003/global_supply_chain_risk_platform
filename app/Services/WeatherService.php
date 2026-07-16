<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    private string $geocodingUrl = 'https://geocoding-api.open-meteo.com/v1/search';
    private string $forecastUrl  = 'https://api.open-meteo.com/v1/forecast';

    public function getWeatherByLocationName(string $locationName): ?array
    {
        $cacheKey = 'weather_' . strtolower($locationName);

        return Cache::remember($cacheKey, 900, function () use ($locationName) {

            $location = $this->geocode($locationName);

            if (! $location) {
                return null;
            }

            $weather = $this->fetchCurrentWeather($location['latitude'], $location['longitude']);

            return array_merge($location, $weather);
        });
    }

    private function geocode(string $name): ?array
    {
        $response = Http::get($this->geocodingUrl, [
            'name'     => $name,
            'count'    => 1,
            'language' => 'en',
            'format'   => 'json',
        ]);

        if (! $response->successful()) {
            return null;
        }

        $result = $response->json('results')[0] ?? null;

        if (! $result) {
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
        $response = Http::get($this->forecastUrl, [
            'latitude'  => $lat,
            'longitude' => $lon,
            'current'   => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
            'timezone'  => 'auto',
        ]);

        if (! $response->successful()) {
            return ['temperature' => null, 'precipitation' => null, 'wind_speed' => null, 'condition' => 'Unknown'];
        }

        $current = $response->json('current', []);
        $code = $current['weather_code'] ?? null;

        return [
            'temperature'    => $current['temperature_2m'] ?? null,
            'precipitation'  => $current['precipitation'] ?? null,
            'wind_speed'     => $current['wind_speed_10m'] ?? null,
            'weather_code'   => $code,
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
