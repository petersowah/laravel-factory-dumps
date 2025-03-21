<?php

namespace PeterSowah\LaravelFactoryDumps;

use PeterSowah\LaravelFactoryDumps\Commands\LaravelFactoryDumpsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelFactoryDumpsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-factory-dumps')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_factory_dumps_table')
            ->hasCommand(LaravelFactoryDumpsCommand::class);
    }
}
