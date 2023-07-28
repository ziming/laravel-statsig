<?php

declare(strict_types=1);

// config for Ziming/LaravelStatsig
use Statsig\Adapters\LocalFileDataAdapter;
use Statsig\Adapters\LocalFileLoggingAdapter;

return [
    'secret' => env('STATSIG_SECRET_KEY', ''),

    'data_adapter' => LocalFileDataAdapter::class,
    'data_adapter_arguments' => [
        // '/tmp/statsig/',
    ],

    'logging_adapter' => LocalFileLoggingAdapter::class,
    'logging_adapter_arguments' => [
        // '/tmp/statsig.logs',
    ],
];
