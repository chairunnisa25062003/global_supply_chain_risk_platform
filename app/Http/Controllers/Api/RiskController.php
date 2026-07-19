<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RiskScoringService;
use App\Services\GNewsService;
use App\Services\SentimentAnalyzer;
use App\Services\WeatherService;
use App\Services\CountryService;
use App\Services\CurrencyService;
use App\Models\RiskScore;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RiskController extends Controller
{
    public function __construct(
        private RiskScoringService $riskScoringService,
        private GNewsService $gNewsService,
        private SentimentAnalyzer $sentimentAnalyzer,
        private WeatherService $weatherService,
        private CountryService $countryService,
        private CurrencyService $currencyService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $country = $request->query('country', 'Germany');

        $profile = $this->countryService->getCountryProfile($country);
        $capital = $profile['capital'] ?? $country;
        $inflationRate = $profile['inflation'] ?? 0;
        $currencyCode = $profile['currency_code'] ?? 'USD';

        $weather = $this->weatherService->getWeatherByLocationName($capital);

        $newsTexts = $this->gNewsService->getNewsTexts($country);
        $sentiment = $this->sentimentAnalyzer->analyzeArticles($newsTexts);

        $currencyChangePercent = 0;
        if ($currencyCode !== 'USD') {
            $currencyChangePercent = $this->calculateCurrencyChangePercent('USD', $currencyCode);
        }

        $rawData = [
            'wind_speed'          => $weather['wind_speed'] ?? 0,
            'storm_alert'         => $weather['is_storm'] ?? false,
            'inflation_rate'      => $inflationRate,
            'news_negative_pct'   => $sentiment['negative_pct'],
            'currency_change_pct' => $currencyChangePercent,
        ];

        $result = $this->riskScoringService->calculate($rawData);

        RiskScore::create([
            'country_name'    => $country,
            'score'           => $result['score'],
            'level'           => $result['level'],
            'weather_score'   => $result['breakdown']['weather'],
            'inflation_score' => $result['breakdown']['inflation'],
            'news_score'      => $result['breakdown']['news'],
            'currency_score'  => $result['breakdown']['currency'],
        ]);

        return response()->json([
            'country'   => $country,
            'score'     => $result['score'],
            'level'     => $result['level'],
            'breakdown' => $result['breakdown'],
            'sentiment' => $sentiment,
        ]);
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
