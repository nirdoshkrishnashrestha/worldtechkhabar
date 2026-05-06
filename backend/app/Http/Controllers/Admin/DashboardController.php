<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\FetchLog;
use App\Models\Source;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'totalArticles' => Article::query()->count(),
            'publishedArticles' => Article::query()->where('status', 'published')->count(),
            'pendingArticles' => Article::query()->where('status', 'pending')->count(),
            'activeSources' => Source::query()->where('is_active', true)->count(),
            'lastFetch' => FetchLog::query()->with('source')->latest('fetched_at')->first(),
            'fetchedToday' => Article::query()->whereDate('fetched_at', today())->count(),
            'recentArticles' => Article::query()->with(['source', 'category'])->latest()->limit(8)->get(),
        ]);
    }
}
