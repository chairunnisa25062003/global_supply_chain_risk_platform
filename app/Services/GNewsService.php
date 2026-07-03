<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class GNewsService
{
    private string $baseUrl = 'https://gnews.io/api/v4/search';

   
    public function getNewsTexts(string $keyword, int $max = 10): array
    {

        $cacheKey = 'gnews_' . strtolower($keyword);

        return Cache::remember($cacheKey, 1800, function () use ($keyword, $max) {
            $apiKey = config('services.gnews.key');

            if (empty($apiKey)) {
                
                return [];
            }

            $response = Http::get($this->baseUrl, [
                'q'      => $keyword . ' economy OR trade OR shipping',
                'lang'   => 'en',
                'max'    => $max,
                'apikey' => $apiKey,
            ]);

            if (! $response->successful()) {
                return [];
            }

            $articles = $response->json('articles', []);

            // Gabungkan judul + deskripsi tiap artikel jadi satu teks
            return collect($articles)
                ->map(fn ($article) => ($article['title'] ?? '') . ' ' . ($article['description'] ?? ''))
                ->filter()
                ->values()
                ->toArray();
        });
    }
}
