<?php

namespace Ziming\LaravelStatsig;

use Illuminate\Foundation\Auth\User;
use Statsig\Adapters\IDataAdapter;
use Statsig\Adapters\ILoggingAdapter;
use Statsig\Adapters\LocalFileDataAdapter;
use Statsig\Adapters\LocalFileLoggingAdapter;
use Statsig\DynamicConfig;
use Statsig\Layer;
use Statsig\StatsigOptions;
use Statsig\StatsigServer;
use Statsig\StatsigUser;

class LaravelStatsig
{
    private readonly IDataAdapter $configAdapter;
    private readonly ?ILoggingAdapter $loggingAdapter;
    private readonly StatsigServer $statsig;

    public function __construct()
    {
        $this->configAdapter = new (config('laravel-statsig.data_adapter'));
        $this->loggingAdapter = new (config('laravel-statsig.logging_adapter'));
        $options = new StatsigOptions($this->configAdapter, $this->loggingAdapter);
        $this->statsig = new StatsigServer(config('laravel-statsig.secret'), $options);
    }

    public function checkGate(StatsigUser|User $user, string $gate): bool
    {
        if ($user instanceof User) {
            $user = $this->convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->checkGate($user, $gate);
    }

    public function getConfig(StatsigUser|User $user, string $config): DynamicConfig
    {
        if ($user instanceof User) {
            $user = $this->convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->getConfig($user, $config);
    }

    public function getExperiment(StatsigUser|User $user, string $experiment): DynamicConfig
    {
        if ($user instanceof User) {
            $user = $this->convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->getExperiment($user, $experiment);
    }

    public function getLayer(StatsigUser|User $user, string $layer): Layer
    {
        if ($user instanceof User) {
            $user = $this->convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->getLayer($user, $layer);
    }

    /**
     * In future this will accept a callable/closure to allow for customizable laravel user to StatsigUser conversion
     */
    private function convertLaravelUserToStatsigUser(User $user, ?callable $conversionCallable = null): StatsigUser
    {
        $statsigUser = StatsigUser::withUserID($user->getAuthIdentifier());
        $statsigUser->setEmail($user->getEmailForVerification());

        if ($conversionCallable !== null) {
            $conversionCallable($user);
        }

        return $statsigUser;
    }

    /**
     * If I missed out anything
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->statsig->$name(...$arguments);
    }
}
