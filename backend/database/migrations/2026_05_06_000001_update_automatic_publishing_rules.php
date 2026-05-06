<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'auto_publish_score_threshold'],
            [
                'value' => '35',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        DB::table('settings')->where('key', 'review_score_threshold')->delete();
    }

    public function down(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'auto_publish_score_threshold'],
            [
                'value' => '80',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'review_score_threshold'],
            [
                'value' => '50',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }
};
