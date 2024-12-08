<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Statsig\StatsigUser;
use Ziming\LaravelStatsig\Facades\LaravelStatsig;

if (! function_exists('statsig_check_gate')) {
    function statsig_check_gate(string $gateName, string|StatsigUser|Authenticatable|null $userOrUserId = null): bool
    {
        if ($userOrUserId === null) {
            return LaravelStatsig::checkGate(Auth::user(), $gateName);
        }

        if (is_string($userOrUserId)) {
            $userOrUserId = StatsigUser::withUserID($userOrUserId);
        }

        return LaravelStatsig::checkGate($userOrUserId, $gateName);
    }
}
