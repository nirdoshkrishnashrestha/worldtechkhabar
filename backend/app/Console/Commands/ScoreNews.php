<?php

namespace App\Console\Commands;

use App\Services\NewsScoringService;
use Illuminate\Console\Command;

class ScoreNews extends Command
{
    protected $signature = 'news:score';

    protected $description = 'Score pending articles.';

    public function handle(NewsScoringService $scoringService): int
    {
        $count = $scoringService->scorePending();
        $this->info("Scored {$count} article(s).");

        return self::SUCCESS;
    }
}
