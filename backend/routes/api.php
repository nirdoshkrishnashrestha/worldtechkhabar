<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SourceController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/news', [ArticleController::class, 'index']);
    Route::get('/news/{slug}', [ArticleController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/sources', [SourceController::class, 'index']);
    Route::get('/latest', [ArticleController::class, 'latest']);
    Route::get('/trending', [ArticleController::class, 'trending']);
    Route::get('/search', [ArticleController::class, 'search']);
});
