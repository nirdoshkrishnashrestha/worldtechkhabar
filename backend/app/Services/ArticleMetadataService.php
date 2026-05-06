<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ArticleMetadataService
{
    public function enrich(Article $article): bool
    {
        if (! $article->original_url || ($article->image_url && $article->summary && $article->content_excerpt)) {
            return false;
        }

        $response = Http::timeout(15)->withHeaders([
            'User-Agent' => 'World Tech Khabar Metadata Fetcher (+https://worldtechkhabar.com)',
            'Accept' => 'text/html,application/xhtml+xml',
        ])->get($article->original_url);

        if (! $response->successful()) {
            return false;
        }

        $metadata = $this->metadataFromHtml($response->body(), $article->original_url);
        $updates = [];

        if (! $article->image_url && ! blank($metadata['image'] ?? null)) {
            $updates['image_url'] = $metadata['image'];
        }

        $description = $this->cleanText($metadata['description'] ?? '');
        if (! $article->summary && $description !== '') {
            $updates['summary'] = Str::limit($description, 320);
        }

        if (! $article->content_excerpt && $description !== '') {
            $updates['content_excerpt'] = Str::words($description, ArticleContentPolicy::MAX_STORED_WORDS, '');
        }

        if (! $article->meta_description && $description !== '') {
            $updates['meta_description'] = Str::limit($description, 155);
        }

        if ($updates === []) {
            return false;
        }

        $article->forceFill($updates)->save();

        return true;
    }

    public function metadataFromHtml(string $html, string $baseUrl): array
    {
        $previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $metadata = [];
        foreach ($dom->getElementsByTagName('meta') as $meta) {
            $key = Str::lower($meta->getAttribute('property') ?: $meta->getAttribute('name'));
            $content = trim($meta->getAttribute('content'));

            if ($key !== '' && $content !== '') {
                $metadata[$key] = $content;
            }
        }

        $image = $metadata['og:image'] ?? $metadata['twitter:image'] ?? $metadata['twitter:image:src'] ?? null;
        $description = $metadata['og:description'] ?? $metadata['twitter:description'] ?? $metadata['description'] ?? null;

        return [
            'image' => $image ? $this->absoluteUrl($image, $baseUrl) : null,
            'description' => $description,
        ];
    }

    private function cleanText(?string $value): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?: '';

        return trim($text);
    }

    private function absoluteUrl(string $url, string $baseUrl): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $base = parse_url($baseUrl);
        $scheme = $base['scheme'] ?? 'https';
        $host = $base['host'] ?? '';

        if (str_starts_with($url, '//')) {
            return $scheme.':'.$url;
        }

        if (str_starts_with($url, '/')) {
            return "{$scheme}://{$host}{$url}";
        }

        $path = isset($base['path']) ? rtrim(str_replace(basename($base['path']), '', $base['path']), '/') : '';

        return "{$scheme}://{$host}{$path}/{$url}";
    }
}
