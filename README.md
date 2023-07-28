# laravel-statsig

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)

Laravel Package for Statsig. A Feature Gate & A/B Testing Platform with a somewhat decent free tier.

This package is still very early in development & likely not ready for production use yet. 
I have only just started using it in production on 2 small sites currently.

Use at your own risk if you want to try it now. But if you have used in production, it would be great to let me know :)

It is basically a wrapper around the [Statsig PHP SDK](https://docs.statsig.com/server/phpSDK)

## Support us

The following features are being considered for the future. If any of it interest you, feel free to submit a PR.

- New Adapters
- New Middlewares
- Convenience Traits & Methods
- HTTP & Console API support
- Octane/Vapor/Serverless Support (Probably far in the future)

Donations are welcomed too as an alternative. Anything goes. 

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
    'data_adapter_arguments' => [
        // '/tmp/statsig/', leave blank for the default directory
    ],

    'logging_adapter' => LocalFileLoggingAdapter::class,
    'logging_adapter_arguments' => [
        // '/tmp/statsig.logs', leave blank for the default file path
    ],
];
```

## Usage

```php
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Statsig\StatsigUser;
use Ziming\LaravelStatsig\Facades\LaravelStatsig;
use Ziming\LaravelStatsig\LaravelStatsigEvent;
use Ziming\LaravelStatsig\LaravelStatsigUserConfiguration;

$laravelStatsig = new Ziming\LaravelStatsig();
$user = Auth::user();
$laravelStatsig->checkGate($user, 'gate_name');

// The Facade Version is fine too
LaravelStatsig::checkGate($user, 'gate_name');

// You can set add this to 1 of your ServiceProviders boot() method to
// override the default laravel user to Statsig user conversion code too if you want
LaravelStatsigUserConfiguration::setConversionCallable(function (User $laravelUser): StatsigUser {
        $statsigUser = StatsigUser::withUserID((string) $laravelUser->getAuthIdentifier());
        $statsigUser->setEmail($laravelUser->getEmailForVerification());
        $statsigUser->setIP(request()->ip());
        $statsigUser->setLocale(App::currentLocale());
        $statsigUser->setUserAgent(request()->userAgent());
        
        // $statsigUser->setCountry('US');
        
        return $statsigUser;
});

// Lastly you can also use LaravelStatsigEvent instead of StatsigEvent
// as it accepts a laravel user object
// I recommend the object action event naming framework
// https://segment.com/academy/collecting-data/naming-conventions-for-clean-data/
$statsigEvent = new LaravelStatsigEvent('event_name');

// You can also use this convenience method
LaravelStatsig::logEventWithAuthUser($statsigEvent);

// or this
$statsigEvent->setUser(Auth::user());
$laravelStatsig->logEvent($statsigEvent);
```

A handy blade directive is also provided to check against Statsig Feature Gates in your frontend blade templates

It is confusingly named in all lowercase to match the official laravel naming conventions for blade directives.

Currently it can only be used if the user is logged in. Do not use it for your guest pages for now.

```blade
@statsigcheckgate('gate_name')
    <p>This is shown if this statsig gate return true</p>
@endstatsigcheckgate
```

Lastly, a helper function is also provided if you want to be even more concise in your blade templates.
It is named in snake case, following laravel naming conventions for global helper functions.

Like the blade directive, currently it can only be used if the user is logged in.

```blade
<div class="{{ statsig_check_gate('awesome_feature') ? 'border-green' : '' }}">
</div>
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
