<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('original_url', 2048)->unique();
            $table->text('summary')->nullable();
            $table->text('content_excerpt')->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->string('author')->nullable();
            $table->timestamp('original_published_at')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('published_on_site_at')->nullable();
            $table->string('hash')->unique();
            $table->enum('status', ['pending', 'review', 'published', 'rejected', 'ignored'])->default('pending');
            $table->integer('score')->default(0);
            $table->text('ai_summary')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_on_site_at']);
            $table->index(['score', 'status']);
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'summary', 'content_excerpt']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
