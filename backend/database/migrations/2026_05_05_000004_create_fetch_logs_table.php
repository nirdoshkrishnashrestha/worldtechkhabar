<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fetch_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['success', 'failed']);
            $table->text('message')->nullable();
            $table->integer('items_found')->default(0);
            $table->integer('items_created')->default(0);
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fetch_logs');
    }
};
