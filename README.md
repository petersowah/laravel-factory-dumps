<p align="center"><img src="/art/social-card.svg" alt="Social Card of Laravel Activity Log"></p>

# Easily export your Eloquent models to Excel and CSV formats.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/petersowah/laravel-factory-dumps.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-factory-dumps)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/petersowah/laravel-factory-dumps/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/petersowah/laravel-factory-dumps/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/petersowah/laravel-factory-dumps/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/petersowah/laravel-factory-dumps/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/petersowah/laravel-factory-dumps.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-factory-dumps)

This package helps with exporting factory generated data and Eloquent collections to csv or xlsx formats.
## Installation

You can install the package via composer:

```bash
composer require petersowah/laravel-factory-dumps
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-factory-dumps-config"
```

This is the contents of the published config file:

```php
return [
 'path' => env('FACTORY_DUMPS_PATH', __DIR__.'/../workbench/database/dumps'),
];
```

## Usage
### Import the `ExportableFactory` trait in your model. Eg.

```php
use PeterSowah\LaravelFactoryDumps\Traits\ExportableFactory;
```

### Use the `toExcel` method to export the data to excel.
```php
$user = User::factory()->create()->toExcel();
```

### Use the `toCsv` method to export the data to csv.
```php
$user = User::factory()->create()->toCsv();
```
### You may also use the method `toExcel` and `toCsv` to export eloquent collections.

```php
$users = User::whereNotNull('email_verified_at')->get();
$users->toExcel();
$users->toCsv();
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

- [petersowah](https://github.com/petersowah)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
