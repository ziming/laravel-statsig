<?php

namespace Ziming\LaravelStatsig\Commands;

use Illuminate\Console\Command;

class StatsigSyncCommand extends Command
{
    public $signature = 'statsig:sync';

    public $description = 'Statsig Sync Command';

    public function handle(): int
    {
        // TODO: Run the Statsig sync command here
        return self::SUCCESS;
    }
}
