<?php

namespace PeterSowah\LaravelFactoryDumps\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use PeterSowah\LaravelFactoryDumps\Collections\ExportableCollection;
use PeterSowah\LaravelFactoryDumps\LaravelFactoryDumps;

trait ExportableFactory
{
    use HasFactory;

    public function newCollection(array $models = []): ExportableCollection
    {
        return new ExportableCollection($models);
    }

    public static function toExcel(?string $fileName = null): string
    {
        $models = static::all();
        return LaravelFactoryDumps::toExcel($models, $fileName);
    }

    public static function toCsv(?string $fileName = null): string
    {
        $models = static::all();
        return LaravelFactoryDumps::toCsv($models, $fileName);
    }
}
