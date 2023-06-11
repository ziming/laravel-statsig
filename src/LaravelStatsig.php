<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig;

use Illuminate\Foundation\Auth\User;
use Statsig\Adapters\IDataAdapter;
use Statsig\Adapters\ILoggingAdapter;
use Statsig\DynamicConfig;
use Statsig\Layer;
use Statsig\StatsigEvent;
use Statsig\StatsigOptions;
use Statsig\StatsigServer;
use Statsig\StatsigUser;
use Ziming\LaravelStatsig\Utils\LaravelUserToStatsigUserConverter;

class LaravelStatsig
{
    private readonly IDataAdapter $configAdapter;

    private readonly ?ILoggingAdapter $loggingAdapter;

    private readonly StatsigServer $statsig;

    public function __construct()
    {
        $this->configAdapter = new (config('statsig.data_adapter'));
        $this->loggingAdapter = new (config('statsig.logging_adapter'));
        $options = new StatsigOptions($this->configAdapter, $this->loggingAdapter);
        $this->statsig = new StatsigServer(config('statsig.secret'), $options);
    }

    public function checkGate(StatsigUser|User $user, string $gate): bool
    {
        if ($user instanceof User) {
            $user = LaravelUserToStatsigUserConverter::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->checkGate($user, $gate);
    }

    public function getConfig(StatsigUser|User $user, string $config): DynamicConfig
    {
        if ($user instanceof User) {
            $user = LaravelUserToStatsigUserConverter::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->getConfig($user, $config);
    }

    public function getExperiment(StatsigUser|User $user, string $experiment): DynamicConfig
    {
        if ($user instanceof User) {
            $user = LaravelUserToStatsigUserConverter::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->getExperiment($user, $experiment);
    }

    public function getLayer(StatsigUser|User $user, string $layer): Layer
    {
        if ($user instanceof User) {
            $user = LaravelUserToStatsigUserConverter::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->getLayer($user, $layer);
    }

    public function logEvent(StatsigEvent $event): void
    {
        $this->statsig->logEvent($event);
    }

    public function getClientInitializeResponse(StatsigUser|user $user): ?array
    {
        if ($user instanceof User) {
            $user = LaravelUserToStatsigUserConverter::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsig->getClientInitializeResponse($user);
    }

    public function __destruct()
    {
        $this->statsig->__destruct();
    }

    public function flush(): void
    {
        $this->statsig->flush();
    }

    /**
     * If I missed out anything or if Statsig added new methods
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->statsig->$name(...$arguments);
    }
}
