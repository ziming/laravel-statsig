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

        // I kept getting file: /tmp/statsig.logs does not exist error
        // but my friend doesn't. So anyway I add this to make sure it exist
        // before we run this command.
        if (file_exists('/tmp/statsig.logs') === false) {
            touch('/tmp/statsig.logs');
        }

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
