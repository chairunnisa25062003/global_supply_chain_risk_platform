<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use App\Services\WeatherService;
use App\Services\CurrencyService;
use App\Services\RiskScoringService;
use App\Services\GNewsService;
use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComparisonController extends Controller
{
    public function __construct(
        private CountryService $countryService,
        private WeatherService $weatherService,
        private CurrencyService $currencyService,
        private RiskScoringService $riskScoringService,
        private GNewsService $gNewsService,
        private SentimentAnalyzer $sentimentAnalyzer,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $country1 = $request->query('country1');
        $country2 = $request->query('country2');

        if (empty($country1) || empty($country2)) {
            return response()->json([
                'message' => 'Parameter "country1" dan "country2" wajib diisi.',
            ], 400);
        }

        $dataA = $this->buildCountryProfile($country1);
        $dataB = $this->buildCountryProfile($country2);

        if (! $dataA || ! $dataB) {
            return response()->json([
                'message' => 'Salah satu negara tidak ditemukan. Cek ejaan nama negaranya.',
            ], 404);
        }

        return response()->json([
            'country1' => $dataA,
            'country2' => $dataB,
        ]);
    }

    private function buildCountryProfile(string $countryName): ?array
    {
        $profile = $this->countryService->getCountryProfile($countryName);

        if (! $profile) {
            return null;
        }


        $weather = $this->weatherService->getWeatherByLocationName($profile['capital'] ?? $countryName);

    
        $newsTexts = $this->gNewsService->getNewsTexts($countryName);
        $sentiment = $this->sentimentAnalyzer->analyzeArticles($newsTexts);

       
        $currencyChangePercent = 0;
        if (($profile['currency_code'] ?? 'USD') !== 'USD') {
            $currencyChangePercent = $this->calculateCurrencyChangePercent('USD', $profile['currency_code']);
        }

        $riskInput = [
            'wind_speed'          => $weather['wind_speed'] ?? 0,
            'storm_alert'         => $weather['is_storm'] ?? false,
            'inflation_rate'      => $profile['inflation'] ?? 0,
            'news_negative_pct'   => $sentiment['negative_pct'] ?? 0,
            'currency_change_pct' => $currencyChangePercent,
        ];

        $risk = $this->riskScoringService->calculate($riskInput);

        return [
            'name'          => $profile['name'],
            'flag'          => $profile['flag'],
            'capital'       => $profile['capital'],
            'population'    => $profile['population'],
            'gdp'           => $profile['gdp'],
            'gdp_year'      => $profile['gdp_year'],
            'inflation'     => $profile['inflation'],
            'currency_code' => $profile['currency_code'],
            'weather'       => [
                'temperature' => $weather['temperature'] ?? null,
                'condition'   => $weather['condition'] ?? 'Unknown',
            ],
            'currency_change_pct' => round($currencyChangePercent, 2),
            'risk_score'          => $risk['score'],
            'risk_level'          => $risk['level'],
        ];
    }

    
    private function calculateCurrencyChangePercent(string $base, string $target): float
    {
        $data = $this->currencyService->getCurrencyData($base, $target);

        if (! $data || count($data['history']) < 2) {
            return 0;
        }

        $first = $data['history'][0]['rate'] ?? null;
        $last = end($data['history'])['rate'] ?? null;

        if (! $first || ! $last) {
            return 0;
        }

        return (($last - $first) / $first) * 100;
    }
}
