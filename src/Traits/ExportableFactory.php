<?php

namespace PeterSowah\LaravelFactoryDumps\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use PeterSowah\LaravelFactoryDumps\Collections\ExportableCollection;

trait ExportableFactory
{
    use HasFactory;

    public function newCollection(array $models = []): ExportableCollection
    {
        return new ExportableCollection($models);
    }
}
