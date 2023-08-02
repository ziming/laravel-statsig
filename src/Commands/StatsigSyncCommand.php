<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
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

        $adapterArguments = config('statsig.data_adapter_arguments');

        foreach ($adapterArguments as $key => $value) {
            $commandToRun .= ' '.$key.' '.$value;
        }

        try {
            $result = Process::run($commandToRun);
            $this->info($result->output());

            return self::SUCCESS;
        } catch (ProcessTimedOutException $e) {
            report(
                new Exception('php ./vendor/statsig/statsigsdk/sync.php has timed out')
            );
        }

        return self::FAILURE;
    }
}
