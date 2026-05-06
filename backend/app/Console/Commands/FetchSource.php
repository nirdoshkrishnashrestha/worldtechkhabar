<?php

namespace App\Console\Commands;

use App\Models\Source;
use App\Services\NewsFetchService;
use Illuminate\Console\Command;

class FetchSource extends Command
{
    protected $signature = 'news:fetch-source {source_id}';

    protected $description = 'Fetch news from a single source.';

    public function handle(NewsFetchService $newsFetchService): int
    {
        $source = Source::query()->findOrFail($this->argument('source_id'));
        $result = $newsFetchService->fetchSource($source);

        $this->info(sprintf(
            '%s: %s, found %d, created %d - %s',
            $result['source'],
            $result['status'],
            $result['found'] ?? 0,
            $result['created'] ?? 0,
            $result['message'] ?? ''
        ));

        return $result['status'] === 'success' ? self::SUCCESS : self::FAILURE;
    }
}
