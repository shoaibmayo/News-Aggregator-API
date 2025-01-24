<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsService;

class FetchArticles extends Command
{
    protected $signature = 'app:fetch_articles';
    protected $description = 'Fetch articles from external APIs and store them';

    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        parent::__construct();
        $this->newsService = $newsService;
    }

    public function handle()
    {
        $this->newsService->fetchFromNewsAPI();
        $this->newsService->fetchFromGuardian();
        $this->newsService->fetchFromNYT();

        $this->info('Articles fetched successfully.');
    }
}