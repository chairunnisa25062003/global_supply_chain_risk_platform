<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    public function __construct(
        private WeatherService $weatherService
    ) {}


    public function index(Request $request): JsonResponse
    {
        $location = $request->query('location');

        if (empty($location)) {
            return response()->json([
                'message' => 'Parameter "location" wajib diisi. Contoh: /api/weather?location=Jakarta',
            ], 400);
        }

        $weather = $this->weatherService->getWeatherByLocationName($location);

        if (! $weather) {
            return response()->json([
                'message' => "Lokasi \"{$location}\" tidak ditemukan.",
            ], 404);
        }

        return response()->json($weather);
    }
}
