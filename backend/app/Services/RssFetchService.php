<?php

namespace App\Services;

use App\Models\Source;
use Illuminate\Support\Facades\Http;

class RssFetchService
{
    public function __construct(private readonly ArticleDataNormalizer $normalizer)
    {
    }

    public function fetch(Source $source): array
    {
        if (blank($source->feed_url)) {
            return ['found' => 0, 'created' => 0, 'message' => 'RSS source has no feed URL.'];
        }

        $response = Http::timeout(20)->withHeaders([
            'User-Agent' => 'World Tech Khabar RSS Fetcher (+https://worldtechkhabar.com)',
            'Accept' => 'application/rss+xml, application/atom+xml, application/xml, text/xml',
        ])->get($source->feed_url);

        if (! $response->successful()) {
            throw new \RuntimeException('Feed request failed with HTTP '.$response->status());
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body());

        if (! $xml) {
            throw new \RuntimeException('Feed XML could not be parsed.');
        }

        $items = $xml->channel->item ?? $xml->entry ?? [];
        $found = 0;
        $created = 0;

        foreach ($items as $item) {
            $found++;

            $payload = $this->payloadFromItem($item);
            if ($article = $this->normalizer->createFromPayload($source, $payload)) {
                $created++;
            }
        }

        return ['found' => $found, 'created' => $created, 'message' => 'RSS fetch completed.'];
    }

    private function payloadFromItem(\SimpleXMLElement $item): array
    {
        $link = (string) ($item->link ?? '');
        if ($link === '' && isset($item->link)) {
            $attributes = $item->link->attributes();
            $link = (string) ($attributes['href'] ?? '');
        }

        $description = $this->descriptionFromItem($item);
        $author = (string) ($item->author->name ?? $item->author ?? '');

        return [
            'title' => (string) ($item->title ?? ''),
            'original_url' => $link,
            'summary' => $description,
            'content_excerpt' => $description,
            'image_url' => $this->normalizer->imageFromNode($item) ?: $this->imageFromHtml($description),
            'author' => $author,
            'original_published_at' => (string) ($item->pubDate ?? $item->published ?? $item->updated ?? ''),
        ];
    }

    private function descriptionFromItem(\SimpleXMLElement $item): string
    {
        $namespaces = $item->getNamespaces(true);

        if (isset($namespaces['content'])) {
            $content = $item->children($namespaces['content']);
            if (! blank((string) ($content->encoded ?? ''))) {
                return (string) $content->encoded;
            }
        }

        return (string) ($item->description ?? $item->summary ?? $item->content ?? '');
    }

    private function imageFromHtml(string $html): ?string
    {
        if (trim($html) === '') {
            return null;
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $image = $dom->getElementsByTagName('img')->item(0);

        return $image?->getAttribute('src') ?: null;
    }
}
