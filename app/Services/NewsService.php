<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Repositories\ArticleRepositoryInterface;

class NewsService
{
    protected $apis;
    private $articleRepository;
    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->apis = config('news');
    }

    public function fetchFromNewsAPI()
    {
        try {
        $response = $this->requestClient('apikey',$this->apis['newsapi']['url'],$this->apis['newsapi']['key']);
        $this->articleRepository->store($response->json()['articles'] ?? [], 'NewsAPI');
        }catch (\Exception $e) {
             Log::error('News API Fetch Error: ' . $e->getMessage());
        }
    }

    public function fetchFromGuardian()
    {
        try {
        $response = $this->requestClient('api-key',$this->apis['guardian']['url'],$this->apis['guardian']['key']);
        $data = json_decode($response->getBody(),true);
        $this->articleRepository->store($data['response']['results'] ?? [], 'Guardian');
        }catch (\Exception $e) {
            Log::error('Guardian Fetch Error: ' . $e->getMessage());
        }
    }

    public function fetchFromNYT()
    {
        try {
        $response = $this->requestClient('api-key',$this->apis['nyt']['url'],$this->apis['nyt']['key']);
        $this->articleRepository->store($response->json()['response']['docs'] ?? [], 'New York Times');
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

}
