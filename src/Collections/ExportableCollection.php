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
     * @return BaseCollection
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
        // Define the default file name if not provided
        $firstItem = $this->first();
        $tableName = is_object($firstItem) && method_exists($firstItem, 'getTable')
            ? $firstItem->getTable()
            : 'export';
        $fileName = $fileName ?? ($tableName.'.csv');

        // Use database_path() for a better path structure
        $filePath = database_path("dumps/csv/{$fileName}");

        // Ensure the directory exists before creating the file
        File::ensureDirectoryExists(database_path('dumps/csv'));

        // Create CSV Writer and write headers and content
        $csv = Writer::createFromPath($filePath, 'w+');
        $firstItemArray = is_object($firstItem) ? $firstItem->toArray() : $firstItem;
        $csv->insertOne(array_keys($firstItemArray)); // Insert headers

        foreach ($this->toArray() as $row) {
            $csv->insertOne($row); // Insert each row of data
        }

        return $filePath;
    }

    /**
     * Export the collection to an Excel file.
     */
    public function toExcel(?string $fileName = null): string
    {
        $firstItem = $this->first();
        $tableName = is_object($firstItem) && method_exists($firstItem, 'getTable')
            ? $firstItem->getTable()
            : 'export';
        $fileName = $fileName ?? ($tableName.'.xlsx');

        $relativePath = 'dumps/excel';
        $fullPath = database_path($relativePath);

        if (! File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }

        Excel::store(
            new ExportFactory($this->toArray()),
            "{$relativePath}/{$fileName}"
        );

        return database_path("{$relativePath}/{$fileName}");
    }
}
