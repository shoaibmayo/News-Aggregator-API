<?php
namespace App\Swagger\Models;
/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     description="Article model that represents a news article.",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the article"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the article"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Content of the article"
 *     ),
 *     @OA\Property(
 *         property="source",
 *         type="string",
 *         description="Source of the article (e.g., BBC, CNN)"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="Category of the article (e.g., Politics, Sports)"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         description="Author of the article"
 *     ),
 *     @OA\Property(
 *         property="published_at",
 *         type="string",
 *         format="date-time",
 *         description="Publication date and time of the article"
 *     )
 * )
 */
