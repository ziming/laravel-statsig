# laravel-statsig

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)

Laravel Package for Statsig. A Feature Gate & A/B Testing Platform with a somewhat decent free tier

This package is still very early in development, but I have used it on a few small production sites for a while.

If you have used in production, it would be great to let me know :)

It is basically a wrapper around the [Statsig PHP SDK](https://docs.statsig.com/server/phpSDK)

## Support us

The following features are being considered for the future. Feel free to suggest or PR others
feel free to submit a PR.

- Custom IDs Support
- More Convenience Traits & Methods
- HTTP & Console API support
- Octane/Vapor/Serverless Support (It probably already works but it is not confirmed)

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
// See Useful References at the end of the read me for some best practices to follow for events naming conventions

$statsigEvent = new LaravelStatsigEvent('event_name');

// Giving it a value is optional
$statsigEvent->setValue('string-or-float-or-int-or-whatever-primitive-type');

// Extra event metadata is optional too. See the official Statsig docs on how many you can send
$statsigEvent->setMetadata([
    'key' => 'value' 
]);

// You can also use this convenience method
LaravelStatsig::logEventWithAuthUser($statsigEvent);

// or this
$statsigEvent->setUser(Auth::user());
$laravelStatsig->logEvent($statsigEvent);
```

A handy blade directive is also provided to check against Statsig Feature Gates in your frontend blade templates

It is confusingly named in all lowercase to match the official laravel naming conventions for blade directives.

```blade
@statsigcheckgate('gate_name')
    <p>This is shown if this statsig gate return true for the authenticated user</p>
@endstatsigcheckgate

@statsigcheckgate('gate_name', 'user-identifier')
    <p>This is usually used for guest user but can be ur authenticated user identifier too</p>
@endstatsigcheckgate

@statsigcheckgate('gate_name', $user)
    <p>$user can be a StatsigUser or a laravel user instance</p>
@endstatsigcheckgate
```

Lastly, a helper function is also provided if you want to be even more concise in your blade templates.
It is named in snake case, following laravel naming conventions for global helper functions.

Like the blade directive, currently it can only be used if the user is logged in.

```blade
<div class="{{ statsig_check_gate('gate_name') ? 'border-red' : '' }}">
The Authenticated user will be used to check if it passes this gate
</div>

<div class="{{ statsig_check_gate('gate_name', 'user-identifier') ? 'border-red' : '' }}">
This is usually used for guest user but can be ur authenticated user identifier too
</div>

<div class="{{ statsig_check_gate('gate_name', $user) ? 'border-red' : '' }}">
Use this is you want to pass in a statsig user or laravel user instance
</div>
```
## Testing

No tests currently, feel free to PR

```bash
composer test
```
## Useful References

Below are links to some good reads that I think would benefit you in getting started:

### Feature Gates
- https://www.statsig.com/blog/feature-gates-101

### Sample Sizes
- https://blog.statsig.com/you-dont-need-large-sample-sizes-to-run-a-b-tests-6044823e9992
- https://www.statsig.com/blog/intro-to-sample-size
- https://www.statsig.com/blog/you-dont-need-millions-of-users-experimentation

### Event Naming Best Practices
- https://segment.com/academy/collecting-data/naming-conventions-for-clean-data/
- https://davidwells.io/blog/clean-analytics
- https://databeats.community/p/product-analytics-components-of-event-data

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
