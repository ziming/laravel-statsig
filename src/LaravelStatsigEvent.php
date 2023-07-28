<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig;

use Illuminate\Contracts\Auth\Authenticatable;
use Statsig\StatsigEvent;
use Statsig\StatsigUser;

class LaravelStatsigEvent extends StatsigEvent
{
    public function setUser(StatsigUser|Authenticatable $user): void
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelStatsigUserConfiguration::convertLaravelUserToStatsigUser($user);
        }

        parent::setUser($user);
    }
}
