<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Statsig\StatsigUser;

class LaravelStatsigUserConfiguration
{
    private static ?Closure $conversionCallback = null;

    public static function defaultConvert(Authenticatable|User $laravelUser): StatsigUser
    {
        $statsigUser = StatsigUser::withUserID(
            (string) $laravelUser->getAuthIdentifier()
        );

        $statsigUser->setEmail($laravelUser->getEmailForVerification());
        $statsigUser->setIP(request()->ip());

        // What is the difference between current locale and get locale?
        // $statsigUser->setLocale(App::currentLocale());
        $statsigUser->setLocale(App::currentLocale());

        // are these set automatically? can i remove?
        $statsigUser->setUserAgent(
            request()
                ->userAgent()
        );

        return $statsigUser;
    }

    public static function setConversionCallable(callable $callable): void
    {
        if (! is_callable($callable)) {
            throw new InvalidArgumentException('This is not a callable');
        }

        self::$conversionCallback = $callable;
    }

    public static function getConversionCallback(): callable
    {
        if (self::$conversionCallback === null) {
            return function (Authenticatable $laravelUser): StatsigUser {
                return self::defaultConvert($laravelUser);
            };
        }

        return self::$conversionCallback;
    }

    public static function convertLaravelUserToStatsigUser(Authenticatable $laravelUser): StatsigUser
    {
        $callback = self::getConversionCallback();

        return $callback($laravelUser);
    }
}
