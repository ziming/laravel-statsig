<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ziming\LaravelStatsig\Commands\StatsigSendCommand;
use Ziming\LaravelStatsig\Commands\StatsigSyncCommand;

class LaravelStatsigServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-statsig')
            ->hasConfigFile()
            ->hasCommands([
                StatsigSyncCommand::class,
                StatsigSendCommand::class,
            ]);
    }
}
