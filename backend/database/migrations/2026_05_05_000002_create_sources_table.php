<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('official_url');
            $table->string('feed_url')->nullable();
            $table->enum('source_type', ['rss', 'api', 'webpage'])->default('rss');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('trust_level', ['official', 'government', 'research', 'open_source', 'company'])->default('official');
            $table->boolean('is_high_priority')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('fetch_frequency_minutes')->default(180);
            $table->timestamp('last_fetched_at')->nullable();
            $table->string('last_fetch_status')->nullable();
            $table->text('last_fetch_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
