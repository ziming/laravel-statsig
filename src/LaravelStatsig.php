<?php

namespace Ziming\LaravelStatsig;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Statsig\Adapters\IDataAdapter;
use Statsig\Adapters\ILoggingAdapter;
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
        $this->configAdapter = new (config('statsig.data_adapter'));
        $this->loggingAdapter = new (config('statsig.logging_adapter'));
        $options = new StatsigOptions($this->configAdapter, $this->loggingAdapter);
        $this->statsig = new StatsigServer(config('statsig.secret'), $options);
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
        if ($conversionCallable === null) {
            $statsigUser = $this->defaultLaravelUserToStatsigUserConversion($user);
        } else {
            $statsigUser = $conversionCallable($user);
        }

        return $statsigUser;
    }

    private function defaultLaravelUserToStatsigUserConversion(User $user): StatsigUser
    {
        $statsigUser = StatsigUser::withUserID($user->getAuthIdentifier());
        $statsigUser->setEmail($user->getEmailForVerification());
        $statsigUser->setStatsigEnvironment([App::environment()]);

        // are these set automatically? can i remove?
        $statsigUser->setUserAgent(request()->userAgent());
        $statsigUser->setIP(request()->ip());

        return $statsigUser;
    }

    /**
     * If I missed out anything or if Statsig added new methods
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->statsig->$name(...$arguments);
    }
}
