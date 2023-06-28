<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig;

use Illuminate\Contracts\Auth\Authenticatable;
use Statsig\StatsigEvent;
use Statsig\StatsigUser;
use Ziming\LaravelStatsig\Utils\LaravelUserToStatsigUserConverter;

class LaravelStatsigEvent extends StatsigEvent
{
    public function setUser(StatsigUser|Authenticatable $user): void
    {
        if ($user instanceof Authenticatable) {
            $user = LaravelUserToStatsigUserConverter::convertLaravelUserToStatsigUser($user);
        }

        parent::setUser($user);
    }
}
