<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="API Docs",
 *     description="Admin API Documentation"
 *   )
 *  @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Use Bearer token to authenticate with Sanctum e.g Bearer 5|mwgDNaieFzEFESPL0QxIlL1xkbhfsw0q1oeob5y0382876da"
 * )
 */
abstract class Controller
{
    //
}
