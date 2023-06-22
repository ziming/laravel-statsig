<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StatsigSendCommand extends Command
{
    public $signature = 'statsig:send';

    public $description = 'Statsig Send Command';

    public function handle(): int
    {
        // TODO: Run the Statsig send command here
        $arguments = [
            '--secret' => config('statsig.secret'),
            '--adapter-class' => escapeshellarg(config('statsig.logging_adapter')),
        ];

        // Hope the package name and folders doesn't change in the future
        $commandToRun = 'php ./vendor/statsig/statsigsdk/send.php';

        foreach ($arguments as $key => $value) {
            $commandToRun .= ' '.$key.' '.$value;
        }

        $result = Process::run($commandToRun);
        $this->info($result->output());

        return $result->exitCode();
    }
}
