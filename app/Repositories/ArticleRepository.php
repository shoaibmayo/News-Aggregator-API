<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function getAllArticles(array $filters, int $pagination)
    {
        $cacheKey = 'articles_' . md5(json_encode($filters)) . "_page_{$pagination}";
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filters, $pagination) {
            $query = Article::query();

            if (!empty($filters['source'])) {
                $query->where('source', $filters['source']);
            }

            if (!empty($filters['category'])) {
                $query->where('category', $filters['category']);
            }

            if (!empty($filters['date_range'])) {
                $dates = explode(',', $filters['date_range']);
                $query->whereBetween('published_at', [$dates[0], $dates[1]]);
            }
            if (!empty($filters['q'])) {
                $query->where('title', 'LIKE', "%{$filters['q']}%")
                      ->orWhere('content', 'LIKE', "%{$filters['q']}%");
            }

            return $query->paginate($pagination);
        });
    }
    public function userPreferences(Request $request)
    {
        $preferences = $request->input('preferences'); 
        $cacheKey = 'articles_preferences_' . md5(json_encode($preferences));
        $articles = Cache::remember($cacheKey, 60 * 10, function () use ($preferences) {
            $query = Article::query();
            if (!empty($preferences['sources'])) {
                $query->whereIn('source', $preferences['sources']);
            }
            if (!empty($preferences['categories'])) {
                $query->whereIn('category', $preferences['categories']);
            }
            if (!empty($preferences['authors'])) {
                $query->whereIn('author', $preferences['authors']);
            }
            return $query->paginate(10);
        });
    
        return response()->json($articles);
    }

    public function store(array $articles, string $source): void
    {
        try {
            foreach ($articles as $article) {
                $publishedAt = $article['publishedAt'] ?? $article['webPublicationDate'] ?? $article['pub_date'] ?? now();
                if (is_string($publishedAt)) {
                    $publishedAt = Carbon::parse($publishedAt)->toDateTimeString();
                }
                Article::updateOrCreate(
                    ['title' => $article['title'] ?? $article['webTitle'] ?? $article['headline']['main']],
                    [
                        'content' => $article['content'] ?? $article['webUrl'] ?? $article['lead_paragraph'] ?? '',
                        'author' => $article['author'] ?? $article['pillarName'] ?? $article['byline']['original'] ?? null,
                        'source' => $source,
                        'category' => $article['category'] ?? $article['headline']['kicker'] ?? 'general',
                        'published_at' => $publishedAt,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Article Storing Error: ' . $e->getMessage());
        }
    }


}