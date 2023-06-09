<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StatsigSyncCommand extends Command
{
    public $signature = 'statsig:sync';

    public $description = 'Statsig Sync Command';

    public function handle(): int
    {
        $arguments = [
            '--secret' => config('statsig.secret'),
            '--adapter-class' => escapeshellarg(config('statsig.data_adapter')),
        ];

        // Hope the package name and folders doesn't change in the future
        $commandToRun = 'php ./vendor/statsig/statsigsdk/sync.php';

        foreach ($arguments as $key => $value) {
            $commandToRun .= ' '.$key.' '.$value;
        }

        $result = Process::run($commandToRun);
        $this->info($result->output());

        return $result->exitCode();
    }
}
