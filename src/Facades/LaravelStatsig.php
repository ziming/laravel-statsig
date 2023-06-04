<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ziming\LaravelStatsig\LaravelStatsig
 */
class LaravelStatsig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ziming\LaravelStatsig\LaravelStatsig::class;
    }
}
