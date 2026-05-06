<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\ArticleMetadataService;
use Illuminate\Console\Command;

class EnrichNews extends Command
{
    protected $signature = 'news:enrich {--limit=100}';

    protected $description = 'Enrich articles missing images or descriptions using official page metadata.';

    public function handle(ArticleMetadataService $metadataService): int
    {
        $updated = 0;
        $limit = (int) $this->option('limit');

        Article::query()
            ->where(function ($query): void {
                $query->whereNull('image_url')
                    ->orWhereNull('summary')
                    ->orWhere('summary', '')
                    ->orWhereNull('content_excerpt')
                    ->orWhere('content_excerpt', '');
            })
            ->latest()
            ->limit($limit)
            ->get()
            ->each(function (Article $article) use ($metadataService, &$updated): void {
                if ($metadataService->enrich($article)) {
                    $updated++;
                    $this->line("Updated #{$article->id}: {$article->title}");
                }
            });

        $this->info("Enriched {$updated} article(s).");

        return self::SUCCESS;
    }
}
