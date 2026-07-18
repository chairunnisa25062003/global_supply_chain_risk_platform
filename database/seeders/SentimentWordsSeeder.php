<?php

namespace Database\Seeders;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Database\Seeder;


class SentimentWordsSeeder extends Seeder
{
    public function run(): void
    {
        $positiveWords = [
            // kata dasar (dari versi awal)
            'growth', 'increase', 'profit', 'stable', 'improve',
            'recovery', 'surge', 'boost', 'expand', 'gain',
            'strong', 'rally', 'record', 'success', 'agreement',
            'partnership', 'investment', 'upgrade', 'positive', 'rise',
            // tambahan: ekonomi & bisnis
            'growth', 'thrive', 'prosper', 'breakthrough', 'milestone',
            'efficient', 'resilient', 'robust', 'optimistic', 'confidence',
            'expansion', 'innovation', 'cooperation', 'alliance', 'deal',
            'exceed', 'outperform', 'accelerate', 'momentum', 'opportunity',
            // tambahan: perdagangan & logistik
            'export', 'trade surplus', 'streamline', 'modernize', 'upgrade',
            'reduce cost', 'on schedule', 'smooth', 'reliable', 'secure',
        ];

        $negativeWords = [
            // kata dasar (dari versi awal)
            'war', 'crisis', 'inflation', 'delay', 'disaster',
            'conflict', 'collapse', 'decline', 'shortage', 'recession',
            'strike', 'shutdown', 'sanction', 'tension', 'disruption',
            'default', 'layoff', 'volatile', 'plunge', 'warning',
            // tambahan: ekonomi & bisnis
            'downturn', 'bankruptcy', 'debt', 'deficit', 'slump',
            'loss', 'cut', 'unemployment', 'instability', 'uncertainty',
            'fraud', 'corruption', 'scandal', 'lawsuit', 'penalty',
            // tambahan: geopolitik
            'unrest', 'protest', 'invasion', 'attack', 'terrorism',
            'embargo', 'tariff', 'dispute', 'threat', 'retaliation',
            // tambahan: perdagangan & logistik
            'congestion', 'bottleneck', 'blockade', 'stranded', 'delayed',
            'damaged', 'seized', 'halted', 'suspended', 'grounded',
        ];

        foreach ($positiveWords as $word) {
            PositiveWord::firstOrCreate(['word' => strtolower($word)]);
        }

        foreach ($negativeWords as $word) {
            NegativeWord::firstOrCreate(['word' => strtolower($word)]);
        }
    }
}
