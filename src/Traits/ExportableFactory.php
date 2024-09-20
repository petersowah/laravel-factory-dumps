<?php

namespace PeterSowah\LaravelFactoryDumps\Traits;


use PeterSowah\LaravelFactoryDumps\Collections\ExportableCollection;

trait ExportableFactory
{
    public function newCollection(array $models = []): ExportableCollection
    {
        return new ExportableCollection($models);
    }
}
