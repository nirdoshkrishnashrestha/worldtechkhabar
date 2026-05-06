<?php

namespace App\Console\Commands;

use App\Services\NewsFetchService;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    protected $signature = 'news:fetch';

    protected $description = 'Fetch news from all active sources that are due for fetching.';

    public function handle(NewsFetchService $newsFetchService): int
    {
        $results = $newsFetchService->fetchDueSources();

        foreach ($results as $result) {
            $this->line(sprintf(
                '%s: %s, found %d, created %d - %s',
                $result['source'],
                $result['status'],
                $result['found'] ?? 0,
                $result['created'] ?? 0,
                $result['message'] ?? ''
            ));
        }

        $this->info('Fetch completed for '.count($results).' source(s).');

        return self::SUCCESS;
    }
}
