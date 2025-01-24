<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;

class ArticleApiTest extends TestCase
{
    public function test_can_fetch_articles()
    {
        $response = $this->getJson('/api/articles');
        $response->assertStatus(200);
    }

    public function test_can_search_articles()
    {
        Article::factory()->create(['title' => 'Breaking News']);

        $response = $this->getJson('/api/articles/search?q=Breaking');
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Breaking News']);
    }
}
