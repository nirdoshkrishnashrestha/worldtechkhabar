<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Setting;

class AutoPublishService
{
    public function __construct(private readonly ArticleContentPolicy $contentPolicy)
    {
    }

    public function publish(): array
    {
        $autoThreshold = (int) Setting::getValue('auto_publish_score_threshold', ArticleContentPolicy::MIN_PUBLISH_SCORE);
        $published = 0;
        $review = 0;
        $ignored = 0;
        $deleted = 0;
        $tooShort = 0;
        $lowScore = 0;
        $duplicate = 0;

        Article::query()->whereIn('status', ['pending', 'review', 'ignored'])->chunkById(100, function ($articles) use ($autoThreshold, &$published, &$ignored, &$deleted, &$tooShort, &$lowScore, &$duplicate): void {
            foreach ($articles as $article) {
                if (! $this->contentPolicy->hasPublishableContent($article)) {
                    $article->delete();
                    $deleted++;
                    $tooShort++;
                    continue;
                }

                if ($article->score < $autoThreshold) {
                    $article->delete();
                    $deleted++;
                    $lowScore++;
                    continue;
                }

                if ($this->isDuplicatePublished($article)) {
                    $article->delete();
                    $deleted++;
                    $duplicate++;
                    continue;
                }

                if ($article->score >= $autoThreshold) {
                    $article->forceFill([
                        'status' => 'published',
                        'published_on_site_at' => now(),
                    ])->save();
                    $published++;
                    continue;
                }
            }
        });

        $ignored = $deleted;

        return compact('published', 'review', 'ignored', 'deleted', 'tooShort', 'lowScore', 'duplicate');
    }

    private function isDuplicatePublished(Article $article): bool
    {
        return Article::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->where(function ($query) use ($article): void {
                $query->where('original_url', $article->original_url)
                    ->orWhere('hash', $article->hash)
                    ->orWhere('title', $article->title);
            })
            ->exists();
    }
}
