<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Cache;

class SentimentAnalyzer
{
    /** @var string[] */
    private array $positiveWords;

    /** @var string[] */
    private array $negativeWords;

    public function __construct()
    {
    
        $this->positiveWords = Cache::remember('sentiment_positive_words', 3600, function () {
            return PositiveWord::pluck('word')->map(fn ($w) => strtolower($w))->toArray();
        });

        $this->negativeWords = Cache::remember('sentiment_negative_words', 3600, function () {
            return NegativeWord::pluck('word')->map(fn ($w) => strtolower($w))->toArray();
        });
    }

   
    public function analyzeText(string $text): array
    {
       
        $cleanText = strtolower($text);
        $cleanText = preg_replace('/[^a-z\s]/', ' ', $cleanText);
        $words = preg_split('/\s+/', trim($cleanText));

        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords, true)) {
                $positiveScore++;
            }
            if (in_array($word, $this->negativeWords, true)) {
                $negativeScore++;
            }
        }

        $sentiment = 'Neutral';
        if ($positiveScore > $negativeScore) {
            $sentiment = 'Positive';
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = 'Negative';
        }

        return [
            'positive'  => $positiveScore,
            'negative'  => $negativeScore,
            'sentiment' => $sentiment,
        ];
    }

    
     /**
     * Analisis BANYAK berita sekaligus (dari hasil GNews API),
     * lalu hasilnya diringkas jadi persentase Positive/Neutral/Negative.
     *
     * Inilah yang nanti dipakai RiskScoringService sebagai `news_negative_pct`.
     *
     * @param string[] $articles array teks berita (judul + deskripsi digabung)
     */
    public function analyzeArticles(array $articles): array
    {
        if (empty($articles)) {
            return [
                'positive_pct' => 0,
                'neutral_pct'  => 100,
                'negative_pct' => 0,
                'total_articles' => 0,
            ];
        }


        $counts = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];

        foreach ($articles as $text) {
            $result = $this->analyzeText($text);
            $counts[$result['sentiment']]++;
        }

        $total = count($articles);

        return [
            'positive_pct'   => round(($counts['Positive'] / $total) * 100),
            'neutral_pct'    => round(($counts['Neutral'] / $total) * 100),
            'negative_pct'   => round(($counts['Negative'] / $total) * 100),
            'total_articles' => $total,
        ];
    }
}
