<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ziming\LaravelStatsig\Facades\LaravelStatsig;

class EnsureStatsigFeaturesAreActive
{
    public function handle(Request $request, Closure $next, string ...$features): void
    {
        if (LaravelStatsig::checkAllGatesAreActive($request->user, $features) === false) {
            // TODO: Allow the user to specify a custom response in the future
            abort(400);
        }
    }
}
