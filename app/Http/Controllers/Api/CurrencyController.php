<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function __construct(
        private CurrencyService $currencyService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $base = strtoupper($request->query('base', 'USD'));
        $target = strtoupper($request->query('target', 'EUR'));

        if ($base === $target) {
            return response()->json([
                'message' => 'Mata uang asal dan tujuan tidak boleh sama.',
            ], 400);
        }

        $data = $this->currencyService->getCurrencyData($base, $target);

        if (! $data) {
            return response()->json([
                'message' => "Kurs {$base} ke {$target} tidak ditemukan.",
            ], 404);
        }

        return response()->json($data);
    }
}
