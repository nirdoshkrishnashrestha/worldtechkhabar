<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Api\InfoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InfoController::class, '__invoke']);
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('sources', SourceController::class)->except('show');
        Route::post('sources/{source}/test', [SourceController::class, 'test'])->name('sources.test');

        Route::resource('articles', ArticleController::class)->only(['index', 'edit', 'update']);
        Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
        Route::post('articles/{article}/score', [ArticleController::class, 'score'])->name('articles.score');
        Route::post('articles/{article}/status/{status}', [ArticleController::class, 'status'])->name('articles.status');

        Route::resource('categories', CategoryController::class)->except('show', 'destroy');
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
