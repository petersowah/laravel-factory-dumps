<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Maatwebsite\Excel\ExcelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use PeterSowah\LaravelFactoryDumps\LaravelFactoryDumpsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'PeterSowah\\LaravelFactoryDumps\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelFactoryDumpsServiceProvider::class,
            ExcelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testbench');
        config()->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('excel', require __DIR__.'/config/excel.php');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-factory-dumps_table.php.stub';
        $migration->up();
        */
    }
}
