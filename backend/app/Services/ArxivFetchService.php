<?php

namespace App\Services;

use App\Models\Source;
use Illuminate\Support\Facades\Http;

class ArxivFetchService
{
    public function __construct(private readonly ArticleDataNormalizer $normalizer)
    {
    }

    public function fetch(Source $source): array
    {
        $category = $source->feed_url ?: 'cs.AI';
        $url = 'https://export.arxiv.org/api/query';
        $response = Http::timeout(20)->withHeaders([
            'User-Agent' => 'World Tech Khabar arXiv Fetcher (+https://worldtechkhabar.com)',
        ])->get($url, [
            'search_query' => 'cat:'.$category,
            'sortBy' => 'submittedDate',
            'sortOrder' => 'descending',
            'start' => 0,
            'max_results' => 20,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('arXiv request failed with HTTP '.$response->status());
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body());

        if (! $xml) {
            throw new \RuntimeException('arXiv response could not be parsed.');
        }

        $found = 0;
        $created = 0;

        foreach ($xml->entry ?? [] as $entry) {
            $found++;
            $authors = [];
            foreach ($entry->author ?? [] as $author) {
                $authors[] = (string) ($author->name ?? '');
            }

            if ($this->normalizer->createFromPayload($source, [
                'title' => (string) $entry->title,
                'original_url' => (string) $entry->id,
                'summary' => (string) $entry->summary,
                'content_excerpt' => (string) $entry->summary,
                'author' => implode(', ', array_filter($authors)),
                'original_published_at' => (string) ($entry->published ?? $entry->updated ?? ''),
                'tags' => ['research', $category, 'arXiv'],
            ])) {
                $created++;
            }
        }

        return ['found' => $found, 'created' => $created, 'message' => 'arXiv fetch completed.'];
    }
}
