<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Utils;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Statsig\StatsigUser;

class LaravelUserToStatsigUserConverter
{
    private static ?Closure $LaravelUserToStatsigUserConversionCallback = null;

    public static function defaultConvert(Authenticatable|User $laravelUser): StatsigUser
    {
        $statsigUser = StatsigUser::withUserID(
            (string) $laravelUser->getAuthIdentifier()
        );

        $statsigUser->setEmail($laravelUser->getEmailForVerification());
        $statsigUser->setStatsigEnvironment([App::environment()]);
        $statsigUser->setIP(request()->ip());

        // What are the difference between current locale and get locale?
        // $statsigUser->setLocale(App::currentLocale());
        $statsigUser->setLocale(App::currentLocale());

        // are these set automatically? can i remove?
        $statsigUser->setUserAgent(request()->userAgent());

        return $statsigUser;
    }

    public static function setLaravelUserToStatsigUserConversionCallback(callable $callable): void
    {
        if (! is_callable($callable)) {
            throw new InvalidArgumentException('This is not a callable');
        }

        self::$LaravelUserToStatsigUserConversionCallback = $callable;
    }

    public static function getLaravelUserToStatsigUserConversionCallback(): callable
    {
        if (self::$LaravelUserToStatsigUserConversionCallback === null) {
            return function (Authenticatable $laravelUser): StatsigUser {
                return self::defaultConvert($laravelUser);
            };
        }

        return self::$LaravelUserToStatsigUserConversionCallback;
    }

    public static function convertLaravelUserToStatsigUser(Authenticatable $laravelUser): StatsigUser
    {
        $callback = self::getLaravelUserToStatsigUserConversionCallback();

        return $callback($laravelUser);
    }
}
