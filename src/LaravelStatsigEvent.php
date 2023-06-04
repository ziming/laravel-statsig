<?php

namespace Ziming\LaravelStatsig;

use Closure;
use Illuminate\Foundation\Auth\User;
use InvalidArgumentException;
use Statsig\Adapters\IDataAdapter;
use Statsig\Adapters\ILoggingAdapter;
use Statsig\DynamicConfig;
use Statsig\Layer;
use Statsig\StatsigEvent;
use Statsig\StatsigOptions;
use Statsig\StatsigServer;
use Statsig\StatsigUser;
use Ziming\LaravelStatsig\Utils\LaravelUserToStatsigUserConverter;

class LaravelStatsigEvent extends StatsigEvent
{
    public function setUser(StatsigUser|User $user): void
    {
        if ($user instanceof User) {
            $user = LaravelUserToStatsigUserConverter::defaultConvert($user);
        }

        parent::setUser($user);
    }
}
