<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Ziming\LaravelStatsig\Facades\LaravelStatsig;

if (! function_exists('statsig_feature_gate')) {
    function statsig_feature_gate(string $name): bool
    {
        return LaravelStatsig::checkGate(Auth::user(), $name);
    }
}
