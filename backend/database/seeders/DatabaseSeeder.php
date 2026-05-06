<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['AI News', 'General artificial intelligence updates from official sources.'],
            ['AI Research', 'Research papers, labs, and science-focused AI updates.'],
            ['AI Tools', 'Developer tools, platforms, and products for AI builders.'],
            ['AI Regulation', 'Policy, standards, safety, and governance updates.'],
            ['Big Tech', 'Major technology company updates related to AI and infrastructure.'],
            ['Startups', 'Startup ecosystem updates from official sources.'],
            ['Open Source', 'Open-source AI projects, releases, and ecosystem news.'],
            ['Hardware', 'AI chips, GPUs, devices, and infrastructure.'],
            ['Cybersecurity', 'Security and risk updates for technology and AI systems.'],
            ['Technology News', 'Broader technology updates from trusted technology publications.'],
        ];

        foreach ($categories as $index => [$name, $description]) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $description,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }

        $categoryId = fn (string $slug) => Category::query()->where('slug', $slug)->value('id');

        $sources = [
            ['OpenAI News', 'https://openai.com/news/', 'https://openai.com/news/rss.xml', 'rss', 'ai-news', 'official', true],
            ['Google AI Blog', 'https://blog.google/technology/ai/', 'https://blog.google/technology/ai/rss/', 'rss', 'ai-news', 'official', true],
            ['Google Research Blog', 'https://research.google/blog/', 'https://research.google/blog/rss/', 'rss', 'ai-research', 'research', true],
            ['Microsoft Research Blog', 'https://www.microsoft.com/en-us/research/blog/', 'https://www.microsoft.com/en-us/research/feed/', 'rss', 'ai-research', 'research', false],
            ['Meta AI Blog', 'https://ai.meta.com/blog/', null, 'webpage', 'ai-research', 'official', true],
            ['NVIDIA Technical Blog', 'https://developer.nvidia.com/blog/', 'https://developer.nvidia.com/blog/feed/', 'rss', 'hardware', 'company', true],
            ['Hugging Face Blog', 'https://huggingface.co/blog', 'https://huggingface.co/blog/feed.xml', 'rss', 'open-source', 'open_source', true],
            ['arXiv cs.AI', 'https://arxiv.org/list/cs.AI/recent', 'cs.AI', 'api', 'ai-research', 'research', true],
            ['arXiv cs.LG', 'https://arxiv.org/list/cs.LG/recent', 'cs.LG', 'api', 'ai-research', 'research', true],
            ['arXiv cs.CL', 'https://arxiv.org/list/cs.CL/recent', 'cs.CL', 'api', 'ai-research', 'research', false],
            ['arXiv cs.CV', 'https://arxiv.org/list/cs.CV/recent', 'cs.CV', 'api', 'ai-research', 'research', false],
            ['NIST AI', 'https://www.nist.gov/artificial-intelligence', 'https://www.nist.gov/news-events/news/rss.xml', 'rss', 'ai-regulation', 'government', true],
            ['European Commission AI', 'https://digital-strategy.ec.europa.eu/en/policies/artificial-intelligence', 'https://digital-strategy.ec.europa.eu/en/rss.xml', 'rss', 'ai-regulation', 'government', true],
            ['GitHub Releases: Ollama', 'https://github.com/ollama/ollama', 'ollama/ollama', 'api', 'open-source', 'open_source', true],
            ['GitHub Releases: LangChain', 'https://github.com/langchain-ai/langchain', 'langchain-ai/langchain', 'api', 'open-source', 'open_source', false],
            ['GitHub Releases: Transformers', 'https://github.com/huggingface/transformers', 'huggingface/transformers', 'api', 'open-source', 'open_source', false],
            ['TechCrunch', 'https://techcrunch.com/', 'https://techcrunch.com/feed/', 'rss', 'technology-news', 'company', true],
        ];

        foreach ($sources as [$name, $officialUrl, $feedUrl, $type, $categorySlug, $trustLevel, $highPriority]) {
            Source::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'official_url' => $officialUrl,
                    'feed_url' => $feedUrl,
                    'source_type' => $type,
                    'category_id' => $categoryId($categorySlug),
                    'trust_level' => $trustLevel,
                    'is_high_priority' => $highPriority,
                    'is_active' => true,
                    'fetch_frequency_minutes' => 180,
                ],
            );
        }

        $settings = [
            'site_name' => 'World Tech Khabar',
            'site_url' => 'https://worldtechkhabar.com',
            'site_description' => 'Verified AI and technology updates from official sources.',
            'auto_publish_score_threshold' => '35',
            'default_fetch_interval' => '180',
            'homepage_featured_category' => 'ai-news',
            'contact_email' => 'contact@worldtechkhabar.com',
            'seo_title' => 'World Tech Khabar - Official AI and Technology News',
            'seo_description' => 'World Tech Khabar summarizes verified AI and technology updates from official sources.',
        ];

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value);
        }

        Setting::query()->where('key', 'review_score_threshold')->delete();

        if ($email = env('ADMIN_EMAIL')) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'World Tech Khabar Admin'),
                    'password' => Hash::make(env('ADMIN_PASSWORD', 'change-this-password')),
                    'is_admin' => true,
                ],
            );
        }
    }
}
