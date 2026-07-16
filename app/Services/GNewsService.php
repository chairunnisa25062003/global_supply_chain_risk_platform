<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class GNewsService
{
    private string $baseUrl = 'https://gnews.io/api/v4/search';

    public function getNewsTexts(string $keyword, int $max = 10): array
    {
        return collect($this->getArticles($keyword, $max))
            ->map(fn ($article) => trim(($article['title'] ?? '') . ' ' . ($article['description'] ?? '')))
            ->filter()
            ->values()
            ->toArray();
    }

    public function getArticles(string $keyword, int $max = 10): array
    {
        $cacheKey = 'gnews_articles_' . strtolower($keyword) . '_' . $max;

        return Cache::remember($cacheKey, 1800, function () use ($keyword, $max) {
            $apiKey = config('services.gnews.key');

            if (empty($apiKey)) {
                return [];
            }

            $response = Http::get($this->baseUrl, [
                'q'      => $keyword,
                'lang'   => 'en',
                'max'    => $max,
                'apikey' => $apiKey,
            ]);

            if (! $response->successful()) {
                return [];
            }

            return $response->json('articles', []);
        });
    }
}
