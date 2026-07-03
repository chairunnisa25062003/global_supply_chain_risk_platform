<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RiskScoringService;
use App\Services\GNewsService;
use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RiskController extends Controller
{
    public function __construct(
        private RiskScoringService $riskScoringService,
        private GNewsService $gNewsService,
        private SentimentAnalyzer $sentimentAnalyzer,
    ) {}

    /**
     * GET /api/risk?country=Germany
     */
    public function index(Request $request): JsonResponse
    {
        $country = $request->query('country', 'Germany');

        // STEP 1: ambil berita terkait negara ini dari GNews
        $newsTexts = $this->gNewsService->getNewsTexts($country);

        // STEP 2: hitung sentiment dari berita-berita itu
        $sentiment = $this->sentimentAnalyzer->analyzeArticles($newsTexts);

        // STEP 3: data lain (cuaca, inflasi, kurs) - sementara masih dummy,
        // nanti diganti pemanggilan Open-Meteo / World Bank / ExchangeRate API
        $rawData = [
            'wind_speed'          => 25,
            'storm_alert'         => false,
            'inflation_rate'      => 4.2,
            'news_negative_pct'   => $sentiment['negative_pct'], // sekarang dari data asli
            'currency_change_pct' => 2.1,
        ];

        // STEP 4: hitung risk score total
        $result = $this->riskScoringService->calculate($rawData);

        return response()->json([
            'country'   => $country,
            'score'     => $result['score'],
            'level'     => $result['level'],
            'breakdown' => $result['breakdown'],
            'sentiment' => $sentiment, // ditampilkan juga biar kelihatan detail analisisnya
        ]);
    }
}
