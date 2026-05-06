<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Setting;
use App\Models\Source;
use App\Services\ArticleDataNormalizer;
use App\Services\AutoPublishService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoPublishServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_publishes_articles_with_score_35_or_higher_and_150_words(): void
    {
        Setting::setValue('auto_publish_score_threshold', '35');
        $article = $this->article(score: 35, words: 150);

        $result = app(AutoPublishService::class)->publish();

        $article->refresh();

        $this->assertSame(1, $result['published']);
        $this->assertSame('published', $article->status);
        $this->assertNotNull($article->published_on_site_at);
    }

    public function test_it_deletes_articles_below_score_35_without_review(): void
    {
        Setting::setValue('auto_publish_score_threshold', '35');
        $article = $this->article(score: 34, words: 150);

        $result = app(AutoPublishService::class)->publish();

        $this->assertSame(0, $result['review']);
        $this->assertSame(1, $result['deleted']);
        $this->assertSame(1, $result['lowScore']);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_it_deletes_short_articles_even_with_high_scores(): void
    {
        Setting::setValue('auto_publish_score_threshold', '35');
        $article = $this->article(score: 100, words: 149);

        $result = app(AutoPublishService::class)->publish();

        $this->assertSame(0, $result['published']);
        $this->assertSame(1, $result['tooShort']);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_it_does_not_create_fetched_articles_under_150_words(): void
    {
        $source = $this->source();

        $article = app(ArticleDataNormalizer::class)->createFromPayload($source, [
            'title' => 'Short fetched article',
            'original_url' => 'https://example.com/short-fetched-article',
            'content_excerpt' => $this->words(149),
        ]);

        $this->assertNull($article);
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_it_does_not_create_fetched_articles_below_score_35(): void
    {
        $source = $this->source([
            'trust_level' => 'company',
            'is_high_priority' => false,
        ]);

        $article = app(ArticleDataNormalizer::class)->createFromPayload($source, [
            'title' => 'General update',
            'original_url' => 'https://example.com/general-update',
            'content_excerpt' => $this->words(150),
        ]);

        $this->assertNull($article);
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_it_creates_fetched_eligible_articles_as_published(): void
    {
        $source = $this->source();

        $article = app(ArticleDataNormalizer::class)->createFromPayload($source, [
            'title' => 'Technology startup launches cloud software platform',
            'original_url' => 'https://example.com/eligible-article',
            'content_excerpt' => $this->words(150, 'software'),
            'original_published_at' => now()->toIso8601String(),
        ]);

        $this->assertNotNull($article);
        $this->assertSame('published', $article->status);
        $this->assertGreaterThanOrEqual(35, $article->score);
        $this->assertNotNull($article->published_on_site_at);
    }

    private function article(int $score, int $words): Article
    {
        $source = $this->source();

        $title = 'Test Article '.uniqid();

        return Article::query()->create([
            'source_id' => $source->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.uniqid(),
            'original_url' => 'https://example.com/'.uniqid(),
            'summary' => 'Summary',
            'content_excerpt' => $this->words($words),
            'fetched_at' => now(),
            'hash' => hash('sha256', $title.uniqid()),
            'status' => 'pending',
            'score' => $score,
        ]);
    }

    private function source(array $overrides = []): Source
    {
        return Source::query()->create(array_merge([
            'name' => 'Test Source',
            'slug' => 'test-source-'.uniqid(),
            'official_url' => 'https://example.com',
            'feed_url' => 'https://example.com/feed',
            'source_type' => 'rss',
            'trust_level' => 'official',
            'is_high_priority' => true,
            'is_active' => true,
            'fetch_frequency_minutes' => 180,
        ], $overrides));
    }

    private function words(int $count, string $word = 'update'): string
    {
        return implode(' ', array_fill(0, $count, $word));
    }
}
