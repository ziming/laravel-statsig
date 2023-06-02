# laravel-statsig

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-statsig/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ziming/laravel-statsig/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ziming/laravel-statsig.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-statsig)

Laravel Package for Statsig.

This package is still very early in development & totally not ready for use yet. Please do not use it.

## Support us

To be added later

## Installation

You can install the package via composer:

```bash
composer require ziming/laravel-statsig
```
Ignore this section.

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-statsig-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-statsig-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using (Ignore this section)

```bash
php artisan vendor:publish --tag="laravel-statsig-views"
```

## Usage

```php
$laravelStatsig = new Ziming\LaravelStatsig();

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
