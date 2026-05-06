<?php

namespace App\Services;

use App\Models\Source;
use Illuminate\Support\Facades\Http;

class GithubReleaseFetchService
{
    public function __construct(private readonly ArticleDataNormalizer $normalizer)
    {
    }

    public function fetch(Source $source): array
    {
        $repo = trim((string) $source->feed_url);
        if ($repo === '' || ! str_contains($repo, '/')) {
            return ['found' => 0, 'created' => 0, 'message' => 'GitHub source feed_url should be owner/repo.'];
        }

        $response = Http::timeout(20)->withHeaders([
            'User-Agent' => 'World Tech Khabar GitHub Release Fetcher',
            'Accept' => 'application/vnd.github+json',
        ])->get("https://api.github.com/repos/{$repo}/releases", ['per_page' => 15]);

        if (! $response->successful()) {
            throw new \RuntimeException('GitHub request failed with HTTP '.$response->status());
        }

        $found = 0;
        $created = 0;

        foreach ($response->json() ?? [] as $release) {
            $found++;
            $name = $release['name'] ?: $release['tag_name'] ?: 'Release';
            if ($this->normalizer->createFromPayload($source, [
                'title' => $source->name.': '.$name,
                'original_url' => $release['html_url'] ?? '',
                'summary' => $release['body'] ?? '',
                'content_excerpt' => $release['body'] ?? '',
                'author' => $release['author']['login'] ?? null,
                'original_published_at' => $release['published_at'] ?? $release['created_at'] ?? null,
                'tags' => ['open source', 'GitHub', 'release'],
            ])) {
                $created++;
            }
        }

        return ['found' => $found, 'created' => $created, 'message' => 'GitHub releases fetch completed.'];
    }
}
