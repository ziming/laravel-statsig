<?php

namespace Ziming\LaravelStatsig\Utils;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Statsig\StatsigUser;
use Closure;
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
}
