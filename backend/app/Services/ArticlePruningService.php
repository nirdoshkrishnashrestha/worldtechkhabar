<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Setting;

class ArticlePruningService
{
    public function __construct(
        private readonly ArticleContentPolicy $contentPolicy,
        private readonly NewsScoringService $scoringService,
    ) {
    }

    public function prune(): array
    {
        $threshold = (int) Setting::getValue('auto_publish_score_threshold', ArticleContentPolicy::MIN_PUBLISH_SCORE);
        $checked = 0;
        $deleted = 0;
        $tooShort = 0;
        $lowScore = 0;
        $published = 0;

        Article::query()->with('source')->chunkById(100, function ($articles) use ($threshold, &$checked, &$deleted, &$tooShort, &$lowScore, &$published): void {
            foreach ($articles as $article) {
                $checked++;
                $score = $this->scoringService->score($article);

                if (! $this->contentPolicy->hasPublishableContent($article)) {
                    $article->delete();
                    $deleted++;
                    $tooShort++;
                    continue;
                }

                if ($score < $threshold) {
                    $article->delete();
                    $deleted++;
                    $lowScore++;
                    continue;
                }

                $article->forceFill([
                    'score' => $score,
                    'status' => 'published',
                    'published_on_site_at' => $article->published_on_site_at ?: now(),
                ])->save();
                $published++;
            }
        });

        return compact('checked', 'deleted', 'tooShort', 'lowScore', 'published');
    }
}
