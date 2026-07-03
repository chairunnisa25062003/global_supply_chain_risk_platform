<?php

namespace App\Services;

class RiskScoringService
{

    private const WEIGHT_WEATHER   = 0.30;
    private const WEIGHT_INFLATION = 0.20;
    private const WEIGHT_NEWS      = 0.40;
    private const WEIGHT_CURRENCY  = 0.10;

    
    private const THRESHOLD_LOW    = 33;
    private const THRESHOLD_MEDIUM = 66;
                                        

    public function calculate(array $data): array
    {
    
        $weatherScore   = $this->weatherRisk($data['wind_speed'] ?? 0, $data['storm_alert'] ?? false);
        $inflationScore = $this->inflationRisk($data['inflation_rate'] ?? 0);
        $newsScore      = $this->newsRisk($data['news_negative_pct'] ?? 0);
        $currencyScore  = $this->currencyRisk($data['currency_change_pct'] ?? 0);

        $total =
            ($weatherScore   * self::WEIGHT_WEATHER) +
            ($inflationScore * self::WEIGHT_INFLATION) +
            ($newsScore      * self::WEIGHT_NEWS) +
            ($currencyScore  * self::WEIGHT_CURRENCY);

        $total = (int) round($total);

        
        [$level, $badgeClass] = $this->levelFor($total);

        return [
            'score'       => $total,
            'level'       => $level,        
            'badge_class' => $badgeClass,   
            'breakdown'   => [
                'weather'   => $weatherScore,
                'inflation' => $inflationScore,
                'news'      => $newsScore,
                'currency'  => $currencyScore,
            ],
        ];
    }


    private function weatherRisk(float $windSpeedKmh, bool $stormAlert): int
    {
    
        $score = min(100, ($windSpeedKmh / 60) * 100);

        if ($stormAlert) {
            $score = min(100, $score + 30); 
        }

        return (int) round($score);
    }

    private function inflationRisk(float $inflationPercent): int
    {
        $score = ($inflationPercent / 20) * 100;

        return (int) max(0, min(100, round($score)));
    }

    private function newsRisk(float $negativeNewsPercent): int
    {
        return (int) max(0, min(100, round($negativeNewsPercent)));
    }


    private function currencyRisk(float $currencyChangePercent): int
    {
        $score = (abs($currencyChangePercent) / 10) * 100;

        return (int) max(0, min(100, round($score)));
    }

    private function levelFor(int $score): array
    {
        if ($score <= self::THRESHOLD_LOW) {
            return ['low', 'risk-low'];
        }

        if ($score <= self::THRESHOLD_MEDIUM) {
            return ['medium', 'risk-medium'];
        }

        return ['high', 'risk-high'];
    }
}
