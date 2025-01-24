<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Repositories\UserPreferenceRepositoryInterface;

/**
 * @OA\Tag(
 *     name="User Preferences",
 *     description="User preferences management operations"
 * )
 */
class UserPreferenceController extends Controller
{
    private $uPreferenceRepository;

    public function __construct(UserPreferenceRepositoryInterface $uPreferenceRepository)
    {
        $this->uPreferenceRepository = $uPreferenceRepository;
    }
    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Retrieve the user's preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of preferences",
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(Request $request)
    {

        $preferences = $this->uPreferenceRepository->show($request);  

        return response()->json($preferences);
    }

    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Set or update user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(Request $request)
    {
        
        $message = $this->uPreferenceRepository->update($request);  
       

        return response()->json(['message' => $message]);
    }
    /**
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     summary="Generate a personalized news feed based on user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of personalized feed",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="source", type="string"),
     *                 @OA\Property(property="category", type="string"),
     *                 @OA\Property(property="author", type="string"),
     *                 @OA\Property(property="published_at", type="string", format="date-time")
     *             )),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No preferences found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No preferences found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function personalizedFeed(Request $request)
    {
        $personalizedArticles = $this->uPreferenceRepository->personalizedFeed($request);
       

        // Return the response outside the cache closure
        if (empty($personalizedArticles)) {
            return response()->json(['message' => 'No articles found'], 404);
        }

        return response()->json($personalizedArticles);
    }
    
}
