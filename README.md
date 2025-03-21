<p align="center"><img src="/art/social-card.svg" alt="Social Card of Laravel Activity Log"></p>

# Easily export your Eloquent models to Excel and CSV formats.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/petersowah/laravel-factory-dumps.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-factory-dumps)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/petersowah/laravel-factory-dumps/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/petersowah/laravel-factory-dumps/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/petersowah/laravel-factory-dumps/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/petersowah/laravel-factory-dumps/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/petersowah/laravel-factory-dumps.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-factory-dumps)

This package helps with exporting factory generated data and Eloquent collections to CSV or Excel formats. It provides a simple and intuitive way to export your data with support for custom column selection and renaming.

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

### Basic Usage

1. Import the `ExportableFactory` trait in your model:

```php
use PeterSowah\LaravelFactoryDumps\Traits\ExportableFactory;

class User extends Model
{
    use ExportableFactory;
    // ...
}
```

2. Export factory-generated data:

```php
// Export to Excel
$users = User::factory(100)->create()->toExcel();

// Export to CSV
$users = User::factory(100)->create()->toCsv();
```

3. Export Eloquent collections:

```php
$users = User::whereNotNull('email_verified_at')->get();
$users->toExcel();
$users->toCsv();
```

### Advanced Usage

#### Custom Filenames

You can specify custom filenames for your exports:

```php
$users = User::factory(100)->create();
$users->toExcel('custom_users.xlsx');
$users->toCsv('custom_users.csv');
```

#### Column Selection and Renaming

The package provides a powerful `pluck` method that allows you to select and rename columns:

```php
$users = User::factory(100)->create();

// Select a single column
$users->pluck('name')->toExcel();

// Select multiple columns
$users->pluck(['name', 'email'])->toExcel();

// Select and rename columns
$users->pluck([
    'name' => 'Full Name',
    'email' => 'Email Address',
    'created_at' => 'Registration Date'
])->toExcel();
```

You can also use Laravel's `select` method to specify which columns to export:

```php
// Select specific columns and export to CSV
$users = User::factory(5)->create()
    ->select(['full_name', 'email'])
    ->toCsv('users-name-email.csv');

// Select specific columns and export to Excel
$users = User::factory(5)->create()
    ->select(['full_name', 'email'])
    ->toExcel('users-name-email.xlsx');
```

#### Custom Column Selection for Excel

You can specify which columns to export to Excel:

```php
$users = User::factory(100)->create();
$users->toExcel(null, ['name', 'email', 'created_at']);
```

### File Locations

- CSV files are stored in `database/dumps/csv/`
- Excel files are stored in `storage/app/dumps/excel/` (or `workbench/database/dumps/excel/` during testing)

The default filename is based on the model's table name (e.g., `users.xlsx` or `users.csv`). For non-model data, it defaults to `export.xlsx` or `export.csv`.

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
