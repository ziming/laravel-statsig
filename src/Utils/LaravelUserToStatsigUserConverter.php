<?php

declare(strict_types=1);

namespace Ziming\LaravelStatsig\Utils;

use Closure;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Statsig\StatsigUser;

class LaravelUserToStatsigUserConverter
{
    public static ?Closure $LaravelUserToStatsigUserConversionCallback;

    public static function defaultConvert(User $laravelUser): StatsigUser
    {
        $statsigUser = StatsigUser::withUserID($laravelUser->getAuthIdentifier());
        $statsigUser->setEmail($laravelUser->getEmailForVerification());
        $statsigUser->setStatsigEnvironment([App::environment()]);
        $statsigUser->setIP(request()->ip());

        // What are the difference between current locale and get locale?
        // $statsigUser->setLocale(App::currentLocale());
        $statsigUser->setLocale(App::getLocale());

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
            return function (User $laravelUser): StatsigUser {
                return self::defaultConvert($laravelUser);
            };
        }

        return self::$LaravelUserToStatsigUserConversionCallback;
    }

    public static function convertLaravelUserToStatsigUser(User $laravelUser): StatsigUser
    {
        $callback = self::getLaravelUserToStatsigUserConversionCallback();

        return $callback($laravelUser);
    }
}
