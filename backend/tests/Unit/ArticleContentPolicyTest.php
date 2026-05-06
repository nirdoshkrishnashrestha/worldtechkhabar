<?php

namespace Tests\Unit;

use App\Services\ArticleContentPolicy;
use PHPUnit\Framework\TestCase;

class ArticleContentPolicyTest extends TestCase
{
    public function test_it_requires_at_least_150_words(): void
    {
        $policy = new ArticleContentPolicy();

        $this->assertFalse($policy->hasPublishableText($this->words(149)));
        $this->assertTrue($policy->hasPublishableText($this->words(150)));
    }

    private function words(int $count): string
    {
        return implode(' ', array_fill(0, $count, 'update'));
    }
}
