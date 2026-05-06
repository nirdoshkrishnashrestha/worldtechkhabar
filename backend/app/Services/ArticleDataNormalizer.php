<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ArticleDataNormalizer
{
    public function __construct(
        private readonly ArticleContentPolicy $contentPolicy,
        private readonly NewsScoringService $scoringService,
    ) {
    }

    public function createFromPayload(Source $source, array $payload): ?Article
    {
        $title = trim((string) ($payload['title'] ?? ''));
        $url = trim((string) ($payload['original_url'] ?? ''));

        if ($title === '' || $url === '') {
            return null;
        }

        $hash = hash('sha256', Str::lower($title).'|'.$url);

        if (Article::query()->where('hash', $hash)->orWhere('original_url', $url)->exists()) {
            return null;
        }

        $excerpt = $this->cleanText($payload['content_excerpt'] ?? $payload['summary'] ?? '');
        if (! $this->contentPolicy->hasPublishableText($excerpt)) {
            return null;
        }

        $summary = $this->summary($payload['summary'] ?? $excerpt);
        $publishedAt = $this->parseDate($payload['original_published_at'] ?? null);
        $score = $this->scoringService->scoreCandidate($source, $title, $summary, $excerpt, $url, $publishedAt);

        if ($score < ArticleContentPolicy::MIN_PUBLISH_SCORE) {
            return null;
        }

        return Article::query()->create([
            'source_id' => $source->id,
            'category_id' => $payload['category_id'] ?? $source->category_id,
            'title' => $title,
            'slug' => $this->uniqueSlug($title),
            'original_url' => $url,
            'summary' => $summary,
            'content_excerpt' => Str::words($excerpt, ArticleContentPolicy::MAX_STORED_WORDS, ''),
            'image_url' => $payload['image_url'] ?? null,
            'author' => Str::limit((string) ($payload['author'] ?? ''), 250, ''),
            'original_published_at' => $publishedAt,
            'fetched_at' => now(),
            'published_on_site_at' => now(),
            'hash' => $hash,
            'status' => 'published',
            'score' => $score,
            'meta_title' => Str::limit($title, 65, ''),
            'meta_description' => Str::limit($summary ?: $excerpt, 155),
            'tags' => $payload['tags'] ?? $this->tagsFromText($title.' '.$excerpt),
        ]);
    }

    public function cleanText(?string $value): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?: '';

        return trim($text);
    }

    public function summary(?string $value): ?string
    {
        $clean = $this->cleanText($value);

        return $clean === '' ? null : Str::limit($clean, 260);
    }

    public function imageFromNode(\SimpleXMLElement $item): ?string
    {
        $namespaces = $item->getNamespaces(true);

        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content)) {
                $attributes = $media->content->attributes();
                if (isset($attributes['url'])) {
                    return (string) $attributes['url'];
                }
            }
        }

        if (isset($item->enclosure)) {
            $attributes = $item->enclosure->attributes();
            if (isset($attributes['url'])) {
                return (string) $attributes['url'];
            }
        }

        return null;
    }

    public function tagsFromText(string $text): array
    {
        $keywords = NewsScoringService::KEYWORDS;
        $lower = Str::lower($text);
        $tags = [];

        foreach ($keywords as $keyword) {
            if (str_contains($lower, Str::lower($keyword))) {
                $tags[] = $keyword;
            }
        }

        return array_values(array_unique(array_slice($tags, 0, 8)));
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug(Str::limit($title, 70, ''));
        $base = $base !== '' ? $base : 'article';
        $candidate = $base.'-'.Str::lower(Str::random(6));

        while (Article::query()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.Str::lower(Str::random(6));
        }

        return $candidate;
    }
}
