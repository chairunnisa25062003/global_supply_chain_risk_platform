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
            'growth', 'increase', 'profit', 'stable', 'improve',
            'recovery', 'surge', 'boost', 'expand', 'gain',
            'strong', 'rally', 'record', 'success', 'agreement',
            'partnership', 'investment', 'upgrade', 'positive', 'rise',
        ];

        $negativeWords = [
            'war', 'crisis', 'inflation', 'delay', 'disaster',
            'conflict', 'collapse', 'decline', 'shortage', 'recession',
            'strike', 'shutdown', 'sanction', 'tension', 'disruption',
            'default', 'layoff', 'volatile', 'plunge', 'warning',
        ];

        foreach ($positiveWords as $word) {
            PositiveWord::firstOrCreate(['word' => $word]);
        }

        foreach ($negativeWords as $word) {
            NegativeWord::firstOrCreate(['word' => $word]);
        }
    }
}
