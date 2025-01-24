<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewsService
{
    protected $apis;
    public function __construct()
    {
        $this->apis = config('news');
    }

    public function fetchFromNewsAPI()
    {
        try {
        $response = $this->requestClient('apikey',$this->apis['newsapi']['url'],$this->apis['newsapi']['key']);
        $this->storeArticles($response->json()['articles'] ?? [], 'NewsAPI');
        }catch (\Exception $e) {
             Log::error('News API Fetch Error: ' . $e->getMessage());
        }
    }

    public function fetchFromGuardian()
    {
        try {
        $response = $this->requestClient('api-key',$this->apis['guardian']['url'],$this->apis['guardian']['key']);
        $data = json_decode($response->getBody(),true);
        $this->storeArticles($data['response']['results'] ?? [], 'Guardian');
        }catch (\Exception $e) {
            Log::error('Guardian Fetch Error: ' . $e->getMessage());
        }
    }

    public function fetchFromNYT()
    {
        try {
        $response = $this->requestClient('api-key',$this->apis['nyt']['url'],$this->apis['nyt']['key']);
        $this->storeArticles($response->json()['response']['docs'] ?? [], 'New York Times');
        }catch (\Exception $e) {
            Log::error('New York Times Fetch Error: ' . $e->getMessage());
        }
    }

    private function requestClient($key, $url,$api_key){
        try {  
            return Http::get($url, [
                    $key =>$api_key,
                    'q' => 'latest',
                    'language' => 'en',
                ]);
        } catch (\Exception $e) {
                Log::error('HTTP Request Error: ' . $e->getMessage());
                return null;
        }        
    }

    private function storeArticles(array $articles, string $source)
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
