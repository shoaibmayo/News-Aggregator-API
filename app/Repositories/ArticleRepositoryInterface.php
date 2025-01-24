<?php

namespace App\Repositories;

interface ArticleRepositoryInterface
{
    public function getAllArticles(array $filters, int $pagination);
    public function userPreferences(Request $request);
    public function store(array $articles, string $source): void;
    
}
