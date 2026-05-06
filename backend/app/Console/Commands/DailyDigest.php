<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class DailyDigest extends Command
{
    protected $signature = 'news:daily-digest';

    protected $description = 'Prepare a simple daily digest summary in the logs.';

    public function handle(): int
    {
        $articles = Article::query()
            ->published()
            ->with(['category', 'source'])
            ->where('published_on_site_at', '>=', now()->subDay())
            ->latest('published_on_site_at')
            ->limit(20)
            ->get();

        $this->info('World Tech Khabar daily digest: '.$articles->count().' published article(s) in the last 24 hours.');

        foreach ($articles as $article) {
            $this->line('- '.$article->title.' ['.($article->source?->name ?? 'Unknown source').']');
        }

        return self::SUCCESS;
    }
}
