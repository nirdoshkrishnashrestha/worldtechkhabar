<?php

namespace App\Console\Commands;

use App\Services\AutoPublishService;
use App\Services\NewsScoringService;
use Illuminate\Console\Command;

class PublishAutoNews extends Command
{
    protected $signature = 'news:publish-auto';

    protected $description = 'Automatically publish eligible pending articles.';

    public function handle(NewsScoringService $scoringService, AutoPublishService $autoPublishService): int
    {
        $scored = $scoringService->scorePending();
        $result = $autoPublishService->publish();

        $this->info("Scored {$scored} article(s).");
        $this->info("Published {$result['published']}, deleted {$result['deleted']} ineligible article(s).");
        $this->info("Deleted details: {$result['tooShort']} too short, {$result['lowScore']} below score 35, {$result['duplicate']} duplicate. Manual review is not used.");

        return self::SUCCESS;
    }
}
