# laravel-statsig

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)

Laravel Package for Statsig.

This package is still very early in development & likely not ready for production use yet. 
Use at your own risk if you want to try it now as breaking changes are likely to happen.

It is basically a wrapper around the [Statsig PHP SDK](https://docs.statsig.com/server/phpSDK)

## Support us

New Adapters will be nice. Donations are welcomed too. Anything goes. 

You can also let Stasig know that [this package referred you there](https://statsig.com/updates#6/12/2023)

## Installation

You can install the package via composer:

```bash
composer require ziming/laravel-statsig
```

Add the following 2 commands to your laravel project `app/Console/Kernel.php`

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Ziming\LaravelStatsig\Commands\StatsigSendCommand;
use Ziming\LaravelStatsig\Commands\StatsigSyncCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(StatsigSyncCommand::class)->everyMinute();
        $schedule->command(StatsigSendCommand::class)->everyMinute();
    }
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="statsig-config"
```

This is the contents of the published config file:

```php
use Statsig\Adapters\LocalFileDataAdapter;
use Statsig\Adapters\LocalFileLoggingAdapter;

return [
    'secret' => env('STATSIG_SECRET_KEY'),

    'data_adapter' => LocalFileDataAdapter::class,
    'logging_adapter' => LocalFileLoggingAdapter::class,
];
```

## Usage

```php
use Illuminate\Foundation\Auth\User;
use Statsig\StatsigUser;
use Ziming\LaravelStatsig\Utils\LaravelUserToStatsigUserConverter;
use Ziming\LaravelStatsig\LaravelStatsigEvent;

$laravelStatsig = new Ziming\LaravelStatsig();

// The Facade Version is fine too
LaravelStatsig::checkGate($user, '<gate_name>');

// You can set add this to your ServiceProvider boot() method to
// override the default laravel user to Statsig user conversion code too if you want
LaravelUserToStatsigUserConverter::setLaravelUserToStatsigUserConversionCallback(function (User $laravelUser): StatsigUser {
        $statsigUser = StatsigUser::withUserID($laravelUser->getAuthIdentifier());
        $statsigUser->setCountry('US');
        
        return $statsigUser;
});

// Lastly you can also use LaravelStatsigEvent instead of StatsigEvent
// as it accepts a laravel user object
$statsigEvent = new LaravelStatsigEvent('event-name');
$statsigUser->setUser(Auth::user());
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ziming](https://github.com/ziming)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
