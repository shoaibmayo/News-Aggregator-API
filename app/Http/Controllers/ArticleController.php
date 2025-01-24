<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NewsService;
use App\Repositories\ArticleRepositoryInterface;

class ArticleController extends Controller
{
    private $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }
    public function index(Request $request)
    {
        $filters = $request->only(['source', 'category', 'date_range','q']);
        $articles = $this->articleRepository->getAllArticles($filters, 10);

        return response()->json($articles);
    }

    public function userPreferences(Request $request)
    {
        $articles = $this->articleRepository->userPreferences($request);

        return response()->json($articles);
    }
}