<?php

namespace App\Services;

use App\Models\FetchLog;
use App\Models\Source;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsFetchService
{
    public function __construct(
        private readonly RssFetchService $rssFetchService,
        private readonly ArxivFetchService $arxivFetchService,
        private readonly GithubReleaseFetchService $githubReleaseFetchService,
        private readonly ArticleMetadataService $metadataService,
        private readonly ArticlePruningService $pruningService,
    ) {
    }

    public function fetchDueSources(): array
    {
        $results = [];

        Source::query()->dueForFetch()->orderByDesc('is_high_priority')->chunkById(25, function ($sources) use (&$results): void {
            foreach ($sources as $source) {
                $results[] = $this->fetchSource($source, false);
            }
        });

        $this->pruningService->prune();

        return $results;
    }

    public function fetchSource(Source $source, bool $pruneAfterFetch = true): array
    {
        try {
            $result = match ($source->source_type) {
                'rss' => $this->rssFetchService->fetch($source),
                'api' => $this->fetchApiSource($source),
                'webpage' => ['found' => 0, 'created' => 0, 'message' => 'Webpage sources are supported as metadata records. Add an RSS/API URL when available for automated fetching.'],
                default => throw new \RuntimeException('Unsupported source type.'),
            };

            $source->forceFill([
                'last_fetched_at' => now(),
                'last_fetch_status' => 'success',
                'last_fetch_error' => null,
            ])->save();

            FetchLog::query()->create([
                'source_id' => $source->id,
                'status' => 'success',
                'message' => $result['message'] ?? null,
                'items_found' => $result['found'] ?? 0,
                'items_created' => $result['created'] ?? 0,
                'fetched_at' => now(),
            ]);

            $this->enrichLatestArticles($source);
            if ($pruneAfterFetch) {
                $this->pruningService->prune();
            }

            return ['source' => $source->name, 'status' => 'success'] + $result;
        } catch (\Throwable $exception) {
            Log::warning('News source fetch failed', ['source_id' => $source->id, 'error' => $exception->getMessage()]);

            $source->forceFill([
                'last_fetched_at' => now(),
                'last_fetch_status' => 'failed',
                'last_fetch_error' => $exception->getMessage(),
            ])->save();

            FetchLog::query()->create([
                'source_id' => $source->id,
                'status' => 'failed',
                'message' => $exception->getMessage(),
                'fetched_at' => now(),
            ]);

            return ['source' => $source->name, 'status' => 'failed', 'message' => $exception->getMessage(), 'found' => 0, 'created' => 0];
        }
    }

    private function fetchApiSource(Source $source): array
    {
        if (Str::startsWith((string) $source->feed_url, 'cs.')) {
            return $this->arxivFetchService->fetch($source);
        }

        if (str_contains((string) $source->official_url, 'github.com')) {
            return $this->githubReleaseFetchService->fetch($source);
        }

        throw new \RuntimeException('API source is not configured for arXiv or GitHub releases.');
    }

    private function enrichLatestArticles(Source $source): void
    {
        $source->articles()
            ->where(function ($query): void {
                $query->whereNull('image_url')
                    ->orWhereNull('summary')
                    ->orWhere('summary', '')
                    ->orWhereNull('content_excerpt')
                    ->orWhere('content_excerpt', '');
            })
            ->latest()
            ->limit(10)
            ->get()
            ->each(fn ($article) => $this->metadataService->enrich($article));
    }
}
