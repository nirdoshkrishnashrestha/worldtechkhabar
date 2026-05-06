<?php

namespace App\Services;

use App\Models\Article;

class ArticleContentPolicy
{
    public const MIN_PUBLISH_WORDS = 150;
    public const MIN_PUBLISH_SCORE = 35;
    public const MAX_STORED_WORDS = 220;

    public function hasPublishableContent(Article $article): bool
    {
        return $this->wordCount((string) $article->content_excerpt) >= self::MIN_PUBLISH_WORDS;
    }

    public function hasPublishableText(?string $text): bool
    {
        return $this->wordCount((string) $text) >= self::MIN_PUBLISH_WORDS;
    }

    public function wordCount(string $text): int
    {
        preg_match_all('/[\p{L}\p{N}]+(?:[\'-][\p{L}\p{N}]+)*/u', strip_tags($text), $matches);

        return count($matches[0]);
    }
}
