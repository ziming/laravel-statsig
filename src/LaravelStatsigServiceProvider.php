<?php

namespace Ziming\LaravelStatsig;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Statsig\Adapters\LocalFileDataAdapter;
use Statsig\Adapters\LocalFileLoggingAdapter;
use Statsig\StatsigOptions;
use Statsig\StatsigServer;
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
            ->hasViews()
            ->hasMigration('create_laravel-statsig_table')
            ->hasCommands([
                StatsigSyncCommand::class,
                StatsigSendCommand::class,
            ]);
    }
}
