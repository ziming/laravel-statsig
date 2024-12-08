<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Statsig\StatsigUser;
use Ziming\LaravelStatsig\Commands\StatsigSendCommand;
use Ziming\LaravelStatsig\Commands\StatsigSyncCommand;
use Ziming\LaravelStatsig\Facades\LaravelStatsig;

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

    public function packageBooted(): void
    {
        Blade::if('statsigcheckgate', function (string $gateName, string|StatsigUser|Authenticatable|null $userOrUserId = null) {
            if ($userOrUserId === null) {
                return LaravelStatsig::checkGate(Auth::user(), $gateName);
            }

            if (is_string($userOrUserId)) {
                $userOrUserId = StatsigUser::withUserID($userOrUserId);
            }

            return LaravelStatsig::checkGate($userOrUserId, $gateName);
        });
    }
}
