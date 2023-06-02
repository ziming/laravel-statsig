<?php

namespace Ziming\LaravelStatsig\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StatsigSyncCommand extends Command
{
    public $signature = 'statsig:sync';

    public $description = 'Statsig Sync Command';

    public function handle(): int
    {
        // TODO: Run the Statsig sync command here
        $arguments = [
            '--secret' => config('statsig.secret'),
            '--adapter-class' => config('statsig.data_adapter'),
            // --adapter-arg Not used yet
        ];

        // Hope the package name and folders doesn't change in the future
        $commandToRun = 'php ./vendor/statsig/statsigsdk/src/sync.php';

        foreach ($arguments as $key => $value) {
            $commandToRun .= ' '.$key.' '.$value;
        }

        $result = Process::run($commandToRun);

        return $result->exitCode();
    }
}
