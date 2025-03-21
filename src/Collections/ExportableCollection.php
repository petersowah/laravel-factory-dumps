<?php

namespace PeterSowah\LaravelFactoryDumps\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\File;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;
use PeterSowah\LaravelFactoryDumps\Exports\ExportFactory;

class ExportableCollection extends Collection
{
    /**
     * Pluck specific columns from the collection.
     *
     * @param  string|array  $value
     * @param  string|null  $key
     */
    public function pluck($value, $key = null): BaseCollection
    {
        $columns = is_array($value) ? $value : [$value];

        return new BaseCollection($this->map(function ($item) use ($columns) {
            return collect($item)->only($columns)->toArray();
        })->all());
    }

    /**
     * Export the collection to a CSV file.
     *
     * @throws UnavailableStream
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function toCsv(?string $fileName = null): string
    {
        $firstItem = $this->first();
        $tableName = $firstItem instanceof \Illuminate\Database\Eloquent\Model
            ? $firstItem->getTable()
            : 'export';
        $fileName = $fileName ?? ($tableName.'.csv');

        $filePath = database_path("dumps/csv/{$fileName}");

        File::ensureDirectoryExists(database_path('dumps/csv'));

        $csv = Writer::createFromPath($filePath, 'w+');
        $firstItemArray = is_object($firstItem) ? $firstItem->toArray() : $firstItem;
        $csv->insertOne(array_keys($firstItemArray));

        foreach ($this->toArray() as $row) {
            $csv->insertOne($row);
        }

        return $filePath;
    }

    /**
     * Export the collection to an Excel file.
     */
    public function toExcel(?string $filename = null, ?array $columns = null): string
    {
        $filename = $filename ?? $this->getDefaultFilename('xlsx');
        $firstItem = $this->first();

        if ($firstItem === null) {
            throw new \RuntimeException('Cannot export an empty collection.');
        }
        
        if ($columns === null) {
            $columns = $firstItem instanceof \Illuminate\Database\Eloquent\Model
                ? $firstItem->getFillable()
                : array_keys(is_array($firstItem) ? $firstItem : $firstItem->toArray());
        }

        $relativePath = 'dumps/excel';
        $basePath = config('factory-dumps.path');
        $fullPath = $basePath.'/'.$relativePath;

        File::ensureDirectoryExists($fullPath);

        Excel::store(
            new ExportFactory($this->pluck($columns)->toArray()),
            "{$relativePath}/{$filename}",
            'default'
        );

        return "{$basePath}/{$relativePath}/{$filename}";
    }

    protected function getDefaultFilename(string $extension): string
    {
        $firstItem = $this->first();
        $tableName = $firstItem instanceof \Illuminate\Database\Eloquent\Model
            ? $firstItem->getTable()
            : 'export';

        return "{$tableName}.{$extension}";
    }
}
