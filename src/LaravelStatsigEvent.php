<?php

namespace Ziming\LaravelStatsig;

use Illuminate\Foundation\Auth\User;
use Statsig\StatsigEvent;
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
