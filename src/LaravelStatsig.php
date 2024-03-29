<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Statsig\Adapters\IDataAdapter;
use Statsig\Adapters\ILoggingAdapter;
use Statsig\DynamicConfig;
use Statsig\Layer;
use Statsig\StatsigEvent;
use Statsig\StatsigOptions;
use Statsig\StatsigServer;
use Statsig\StatsigUser;

class LaravelStatsig
{
    private readonly IDataAdapter $configAdapter;

    private readonly ?ILoggingAdapter $loggingAdapter;

    private readonly StatsigServer $statsigServer;

    public function __construct()
    {
        $this->configAdapter = new (config('statsig.data_adapter'));
        $this->loggingAdapter = new (config('statsig.logging_adapter'));
        $options = new StatsigOptions($this->configAdapter, $this->loggingAdapter);

        if (App::isLocal()) {
            $options->setEnvironmentTier('development');
        } else {
            $options->setEnvironmentTier(App::environment());
        }

        $this->statsigServer = new StatsigServer(config('statsig.secret'), $options);
    }

    public function checkGate(StatsigUser|Authenticatable $user, string $gate): bool
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsigServer->checkGate($user, $gate);
    }

    public function checkAllGatesAreActive(StatsigUser|Authenticatable $user, array $gates): bool
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        foreach ($gates as $gate) {
            if ($this->statsigServer->checkGate($user, $gate) === false) {
                return false;
            }
        }

        return true;
    }

    public function checkSomeGatesAreActive(StatsigUser|Authenticatable $user, array $gates): bool
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        foreach ($gates as $gate) {
            if ($this->statsigServer->checkGate($user, $gate) === true) {
                return true;
            }
        }

        return false;
    }

    public function getConfig(StatsigUser|Authenticatable $user, string $config): DynamicConfig
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsigServer->getConfig($user, $config);
    }

    public function getExperiment(StatsigUser|Authenticatable $user, string $experiment): DynamicConfig
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsigServer->getExperiment($user, $experiment);
    }

    public function getLayer(StatsigUser|Authenticatable $user, string $layer): Layer
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsigServer->getLayer($user, $layer);
    }

    public function logEvent(StatsigEvent $event): void
    {
        $this->statsigServer->logEvent($event);
    }

    public function logEventWithAuthUser(LaravelStatsigEvent $event): void
    {
        $event->setUser(Auth::user());
        $this->statsigServer->logEvent($event);
    }

    public function getClientInitializeResponse(StatsigUser|Authenticatable $user): ?array
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        return $this->statsigServer->getClientInitializeResponse($user);
    }

    public function __destruct()
    {
        $this->statsigServer->__destruct();
    }

    public function flush(): void
    {
        $this->statsigServer->flush();
    }

    /**
     * If I missed out anything or if Statsig added new methods to the Statsig class
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (isset($arguments[0]) && $arguments[0] instanceof Authenticatable) {
            $arguments[0] = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($arguments[0]);
        }

        return $this->statsigServer->$name(...$arguments);
    }
}
