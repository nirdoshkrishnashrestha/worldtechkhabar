<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Str;

class NewsScoringService
{
    public const KEYWORDS = [
        'AI',
        'artificial intelligence',
        'machine learning',
        'LLM',
        'large language model',
        'GPT',
        'Claude',
        'Gemini',
        'model',
        'safety',
        'regulation',
        'benchmark',
        'open source',
        'dataset',
        'research',
        'chip',
        'GPU',
        'inference',
        'training',
        'robotics',
        'automation',
        'cybersecurity',
        'cloud',
        'startup',
        'software',
        'hardware',
        'technology',
        'tech',
        'apps',
        'mobile',
        'enterprise',
        'venture',
        'funding',
        'fintech',
        'commerce',
        'ecommerce',
        'privacy',
        'semiconductor',
        'internet',
        'social media',
        'space',
        'electric vehicle',
    ];

    public function score(Article $article): int
    {
        $article->loadMissing('source');

        return $this->scoreCandidate(
            $article->source,
            (string) $article->title,
            (string) $article->summary,
            (string) $article->content_excerpt,
            (string) $article->original_url,
            $article->original_published_at,
            $article->id,
        );
    }

    public function scoreCandidate(
        ?Source $source,
        string $title,
        ?string $summary,
        ?string $contentExcerpt,
        ?string $originalUrl,
        ?Carbon $originalPublishedAt,
        ?int $ignoreArticleId = null,
    ): int {
        $score = 0;

        if (in_array($source?->trust_level, ['official', 'government', 'research', 'open_source'], true)) {
            $score += 30;
        }

        if ($source?->is_high_priority) {
            $score += 20;
        }

        $text = Str::lower($title.' '.$summary.' '.$contentExcerpt);
        $keywordScore = 0;
        foreach (self::KEYWORDS as $keyword) {
            if (str_contains($text, Str::lower($keyword))) {
                $keywordScore += 5;
            }
        }
        $score += min($keywordScore, 30);

        if ($originalPublishedAt?->greaterThanOrEqualTo(now()->subHours(48))) {
            $score += 10;
        }

        if ($originalUrl && $originalPublishedAt) {
            $score += 10;
        }

        if ($this->hasSimilarTitle($title, $ignoreArticleId)) {
            $score -= 50;
        }

        if ($originalPublishedAt?->lessThan(now()->subDays(30))) {
            $score -= 30;
        }

        if ($keywordScore === 0) {
            $score -= 40;
        }

        return max(0, min(100, $score));
    }

    public function scorePending(): int
    {
        $count = 0;

        Article::query()
            ->whereIn('status', ['pending', 'review'])
            ->with('source')
            ->chunkById(100, function ($articles) use (&$count): void {
                foreach ($articles as $article) {
                    $article->forceFill(['score' => $this->score($article)])->save();
                    $count++;
                }
            });

        return $count;
    }

    private function hasSimilarTitle(string $title, ?int $ignoreArticleId = null): bool
    {
        $words = collect(explode(' ', Str::lower($title)))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => strlen($word) > 4)
            ->take(6);

        if ($words->count() < 3) {
            return false;
        }

        return Article::query()
            ->when($ignoreArticleId, fn ($query) => $query->where('id', '!=', $ignoreArticleId))
            ->where(function ($query) use ($words): void {
                foreach ($words as $word) {
                    $query->where('title', 'like', '%'.$word.'%');
                }
            })
            ->exists();
    }
}
