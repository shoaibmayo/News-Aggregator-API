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

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get all articles with filters",
     *     description="Retrieve a list of articles based on filters such as source, category, date range, and search query.",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter by article source",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by article category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_range",
     *         in="query",
     *         description="Filter by date range (e.g., '2023-01-01,2023-12-31')",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search by keyword",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent()
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->only(['source', 'category', 'date_range','q']);
        $articles = $this->articleRepository->getAllArticles($filters, 10);

        return response()->json($articles);
    }


    /**
     * @OA\Get(
     *     path="/api/articles/preferences",
     *     summary="Get articles based on user preferences",
     *     description="Retrieve articles based on user-defined preferences such as sources, categories, and authors.",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="preferences",
     *         in="query",
     *         description="User preferences for filtering articles",
     *         required=false,
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles based on preferences",
     *         @OA\JsonContent()
     *         )
     *     )
     * )
     */
    public function userPreferences(Request $request)
    {
        $preferences = $request->input('preferences'); 
        $articles = $this->articleRepository->userPreferences($preferences);

        return response()->json($articles);
    }
}