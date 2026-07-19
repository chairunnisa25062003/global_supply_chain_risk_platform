<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\NewsCache;


class GNewsService
{
    private string $baseUrl = 'https://gnews.io/api/v4/search';
    private const CACHE_MINUTES = 30;

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
        $keyword = strtolower(trim($keyword));

        // STEP 1: cek tabel news_cache dulu
        $cached = NewsCache::where('keyword', $keyword)->first();

        if ($cached && $cached->fetched_at->diffInMinutes(now()) < self::CACHE_MINUTES) {
            return $cached->articles;
        }

        // STEP 2: cache tidak ada / sudah basi -> panggil API
        $apiKey = config('services.gnews.key');

        if (empty($apiKey)) {
      
            return $cached ? $cached->articles : [];
        }

        $response = Http::get($this->baseUrl, [
            'q'      => $keyword,
            'lang'   => 'en',
            'max'    => $max,
            'apikey' => $apiKey,
        ]);

        if (! $response->successful()) {
            return $cached ? $cached->articles : [];
        }

        $articles = $response->json('articles', []);

        // STEP 3: simpan/update ke tabel database
        NewsCache::updateOrCreate(
            ['keyword' => $keyword],
            ['articles' => $articles, 'fetched_at' => now()]
        );

        return $articles;
    }
}
