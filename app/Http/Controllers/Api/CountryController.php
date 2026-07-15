<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function __construct(
        private CountryService $countryService
    ) {}

    /**
     * GET /api/countries?country=Germany
     */
    public function index(Request $request): JsonResponse
    {
        $countryName = $request->query('country');

        if (empty($countryName)) {
            return response()->json([
                'message' => 'Parameter "country" wajib diisi. Contoh: /api/countries?country=Germany',
            ], 400);
        }

        $profile = $this->countryService->getCountryProfile($countryName);

        if (! $profile) {
            return response()->json([
                'message' => "Negara \"{$countryName}\" tidak ditemukan.",
            ], 404);
        }

        return response()->json($profile);
    }
}
