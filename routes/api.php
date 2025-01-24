<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;

// throttling middleware to rate limit on requests
Route::middleware('throttle:60,1')->group(function () {
    Route::post('register', [UserController::class, 'register']); 
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum'); //use auth sanctum middleware for authorized users
    Route::post('forgot-password', [UserController::class, 'sendResetLink']);
    Route::post('reset-password', [UserController::class, 'resetPassword']);
});

//protected routes
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/preferences', [ArticleController::class, 'userPreferences']);

    Route::get('preferences', [UserPreferenceController::class, 'show']); 
    Route::post('preferences', [UserPreferenceController::class, 'update']);  
    Route::get('personalized-feed', [UserPreferenceController::class, 'personalizedFeed']);
});