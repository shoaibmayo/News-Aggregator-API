<?php

namespace App\Repositories;

interface ArticleRepositoryInterface
{
    public function getAllArticles(array $filters, int $pagination);
    public function userPreferences($preferences);
    public function store(array $articles, string $source): void;
    
}
