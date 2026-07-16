<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GNewsService;
use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function __construct(
        private GNewsService $gNewsService,
        private SentimentAnalyzer $sentimentAnalyzer,
    ) {}

   
    public function index(Request $request): JsonResponse
    {
        $keyword = $request->query('keyword', 'supply chain logistics trade');

        $articles = $this->gNewsService->getArticles($keyword, 12);

        $result = collect($articles)->map(function ($article) {
            $text = trim(($article['title'] ?? '') . ' ' . ($article['description'] ?? ''));
            $sentiment = $this->sentimentAnalyzer->analyzeText($text);

            return [
                'title'        => $article['title'] ?? '-',
                'description'  => $article['description'] ?? '-',
                'url'          => $article['url'] ?? '#',
                'image'        => $article['image'] ?? null,
                'source'       => $article['source']['name'] ?? 'Unknown',
                'published_at' => $article['publishedAt'] ?? null,
                'sentiment'    => $sentiment['sentiment'], 
            ];
        });

        return response()->json([
            'keyword'  => $keyword,
            'total'    => $result->count(),
            'articles' => $result->values(),
        ]);
    }
}
