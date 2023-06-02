<?php

// config for Ziming/LaravelStatsig
use Statsig\Adapters\LocalFileDataAdapter;
use Statsig\Adapters\LocalFileLoggingAdapter;

return [
    'secret' => env('STATSIG_SECRET_KEY'),

    'data_adapter' => LocalFileDataAdapter::class,
    'logging_adapter' => LocalFileLoggingAdapter::class,
];
