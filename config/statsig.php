<?php

declare(strict_types=1);

// config for Ziming/LaravelStatsig
use Statsig\Adapters\LocalFileDataAdapter;
use Statsig\Adapters\LocalFileLoggingAdapter;

return [
    'secret' => env('STATSIG_SECRET_KEY', ''),

    'data_adapter' => LocalFileDataAdapter::class,

    // arguments to the Data Adapter class constructor
    'data_adapter_arguments' => [
        // '/tmp/statsig/', // empty array for the default directory for the default Data Adapter
    ],

    'logging_adapter' => LocalFileLoggingAdapter::class,

    // arguments to the Logging Adapter class constructor
    'logging_adapter_arguments' => [
        // '/tmp/statsig.logs', // empty array for the default file path for the default Logging Adapter
    ],
];
