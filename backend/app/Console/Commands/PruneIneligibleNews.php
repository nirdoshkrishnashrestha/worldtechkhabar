<?php

namespace App\Console\Commands;

use App\Services\ArticlePruningService;
use Illuminate\Console\Command;

class PruneIneligibleNews extends Command
{
    protected $signature = 'news:prune-ineligible';

    protected $description = 'Delete articles that do not meet the publishing score and content rules.';

    public function handle(ArticlePruningService $pruningService): int
    {
        $result = $pruningService->prune();

        $this->info("Checked {$result['checked']} article(s).");
        $this->info("Deleted {$result['deleted']} ineligible article(s): {$result['tooShort']} too short, {$result['lowScore']} below score 35.");
        $this->info("Published/kept {$result['published']} eligible article(s).");

        return self::SUCCESS;
    }
}
