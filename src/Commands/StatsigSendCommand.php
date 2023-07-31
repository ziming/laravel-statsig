<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Statsig\Adapters\LocalFileLoggingAdapter;

class StatsigSendCommand extends Command
{
    public $signature = 'statsig:send';

    public $description = 'Statsig Send Command';

    public function handle(): int
    {

        // I kept getting file: /tmp/statsig.logs does not exist error
        // So anyway I add this to make sure it exist before we run Statsig Send Command.

        if (config('statsig.logging_adapter') === LocalFileLoggingAdapter::class) {
            $argumentCount = count(config('statsig.logging_adapter_arguments'));

            if ($argumentCount === 0) {
                $loggingAdapterFilePath = '/tmp/statsig.logs';
            } else {
                $loggingAdapterFilePath = config('statsig.logging_adapter_arguments')[0];
            }

            if (file_exists($loggingAdapterFilePath) === false) {
                touch($loggingAdapterFilePath);
            }
        }

        $arguments = [
            '--secret' => config('statsig.secret'),
            '--adapter-class' => escapeshellarg(config('statsig.logging_adapter')),
        ];

        // Hope the package name and folders doesn't change in the future
        $commandToRun = 'php ./vendor/statsig/statsigsdk/send.php';

        foreach ($arguments as $key => $value) {
            $commandToRun .= ' '.$key.' '.$value;
        }

        $adapterArguments = config('statsig.logging_adapter_arguments');

        foreach ($adapterArguments as $key => $value) {
            $commandToRun .= ' '.$key.' '.$value;
        }

        $result = Process::run($commandToRun);
        $this->info($result->output());

        return $result->exitCode();
    }
}
