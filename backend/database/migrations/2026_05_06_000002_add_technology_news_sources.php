<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')->updateOrInsert(
            ['slug' => 'technology-news'],
            [
                'name' => 'Technology News',
                'description' => 'Broader technology updates from trusted technology publications.',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        $categoryId = DB::table('categories')->where('slug', 'technology-news')->value('id');

        DB::table('sources')->updateOrInsert(
            ['slug' => Str::slug('TechCrunch')],
            [
                'name' => 'TechCrunch',
                'official_url' => 'https://techcrunch.com/',
                'feed_url' => 'https://techcrunch.com/feed/',
                'source_type' => 'rss',
                'category_id' => $categoryId,
                'trust_level' => 'company',
                'is_high_priority' => true,
                'is_active' => true,
                'fetch_frequency_minutes' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('sources')->where('slug', Str::slug('TechCrunch'))->delete();
        DB::table('categories')->where('slug', 'technology-news')->delete();
    }
};
