<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\WeatherCache;


class WeatherService
{
    private string $geocodingUrl = 'https://geocoding-api.open-meteo.com/v1/search';
    private string $forecastUrl  = 'https://api.open-meteo.com/v1/forecast';
    private const CACHE_MINUTES = 15;

    public function getWeatherByLocationName(string $locationName): ?array
    {
        $cached = WeatherCache::where('location_name', $locationName)->first();

        if ($cached && $cached->fetched_at->diffInMinutes(now()) < self::CACHE_MINUTES) {
            return $this->toArray($cached);
        }

        $location = $this->geocode($locationName);

        if (! $location) {
            return $cached ? $this->toArray($cached) : null;
        }

        $weather = $this->fetchCurrentWeather($location['latitude'], $location['longitude']);
        $fullData = array_merge($location, $weather);

        $record = WeatherCache::updateOrCreate(
            ['location_name' => $locationName],
            [
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

    private function toArray(WeatherCache $cache): array
    {
        return [
            'location_name' => $cache->location_name,
            'country'       => '-', 
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
            return ['temperature' => null, 'precipitation' => null, 'wind_speed' => null, 'condition' => 'Unknown', 'is_storm' => false];
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
