<?php

namespace PeterSowah\LaravelFactoryDumps\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PeterSowah\LaravelFactoryDumps\LaravelFactoryDumps
 */
class LaravelFactoryDumps extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \PeterSowah\LaravelFactoryDumps\LaravelFactoryDumps::class;
    }
}
