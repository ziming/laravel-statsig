<?php

namespace Ziming\LaravelStatsig\Commands;

use Illuminate\Console\Command;

class StatsigSendCommand extends Command
{
    public $signature = 'statsig:send';

    public $description = 'Statsig Send Command';

    public function handle(): int
    {
        // TODO: Run the Statsig send command here
        return self::SUCCESS;
    }
}
