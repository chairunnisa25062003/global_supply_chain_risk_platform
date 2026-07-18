<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EconomyController extends Controller
{
    public function __construct(
        private CountryService $countryService
    ) {}

   
    public function index(Request $request): JsonResponse
    {
        $countryName = $request->query('country', 'Germany');

        $data = $this->countryService->getHistoricalIndicators($countryName);

        if (! $data) {
            return response()->json([
                'message' => "Data historis untuk \"{$countryName}\" tidak ditemukan.",
            ], 404);
        }

        return response()->json($data);
    }
}
