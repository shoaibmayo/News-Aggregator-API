<?php

namespace App\Repositories;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserPreferenceRepository implements UserPreferenceRepositoryInterface
{
    public function show($request){
       return $request->user()->preferences;
    }
    public function update($request){
        $validated = $request->validate([
            'preferred_sources' => 'array|nullable',
            'preferred_categories' => 'array|nullable',
            'preferred_authors' => 'array|nullable',
        ]);
        $user = $request->user();

        // Update or create the user's preferences
        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );
        return "Preferences updated successfully";
    }
    public function personalizedFeed($request){
        $user = $request->user();
        $cacheKey = 'personalized_feed_' . $user->id;
        return Cache::remember($cacheKey, 60, function () use ($user) {
            $preferences = $user->preferences;

            if (!$preferences) {
                return []; 
            }

            // Fetch articles based on user's preferences
            $articlesQuery = Article::query();

            if ($preferences->preferred_sources && count($preferences->preferred_sources) > 0) {
                $articlesQuery->whereIn('source', $preferences->preferred_sources);
            }
            
            if ($preferences->preferred_categories && count($preferences->preferred_categories) > 0) {
                $articlesQuery->whereIn('category', $preferences->preferred_categories);
            }
            
            if ($preferences->preferred_authors && count($preferences->preferred_authors) > 0) {
                $articlesQuery->whereIn('author', $preferences->preferred_authors);
            }
            return $articlesQuery->orderBy('published_at', 'desc')->paginate(10);
        });
    }
}
