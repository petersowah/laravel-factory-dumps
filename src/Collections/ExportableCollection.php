<?php

namespace PeterSowah\LaravelFactoryDumps\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\File;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;
use PeterSowah\LaravelFactoryDumps\Exports\ExportFactory;
use RuntimeException;

class ExportableCollection extends Collection
{
    /**
     * Pluck specific columns from the collection.
     *
     * @param  string|array  $value
     * @param  string|null  $key
     */
    public function pluck($value, $key = null): SupportCollection
    {
        if (is_string($value)) {
            return $this->map(function ($item) use ($value) {
                $asArray = $this->itemToArray($item);

                return [$value => $asArray[$value] ?? null];
            });
        }

        $columns = $value;
        $hasCustomNames = array_filter($columns, 'is_string', ARRAY_FILTER_USE_KEY);

        return $this->map(function ($item) use ($columns, $hasCustomNames) {
            $asArray = $this->itemToArray($item);

            if ($hasCustomNames) {
                $result = array_intersect_key($asArray, array_flip(array_keys($columns)));
                $renamed = [];
                foreach ($result as $originalKey => $originalValue) {
                    $renamed[$columns[$originalKey]] = $originalValue;
                }

                return $renamed;
            }

            return array_intersect_key($asArray, array_flip($columns));
        });
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

        if ($firstItem === null) {
            throw new RuntimeException('Cannot export an empty collection.');
        }

        $tableName = $firstItem instanceof Model
            ? $firstItem->getTable()
            : 'export';
        $fileName = $fileName ?? ($tableName.'.csv');

        $filePath = database_path("dumps/csv/{$fileName}");

        File::ensureDirectoryExists(database_path('dumps/csv'));

        $csv = Writer::createFromPath($filePath, 'w+');
        $firstItemArray = $this->itemToArray($firstItem);
        $headers = array_keys($firstItemArray);
        $csv->insertOne($headers);

        foreach ($this->all() as $row) {
            $rowArray = $this->itemToArray($row);
            $ordered = [];
            foreach ($headers as $header) {
                $ordered[] = $rowArray[$header] ?? null;
            }
            $csv->insertOne($ordered);
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
            throw new RuntimeException('Cannot export an empty collection.');
        }

        if ($columns === null) {
            if ($firstItem instanceof Model) {
                $columns = $firstItem->getFillable();
                if ($columns === []) {
                    $columns = array_keys($firstItem->toArray());
                }
            } else {
                $firstItemArray = $this->itemToArray($firstItem);
                $columns = array_keys($firstItemArray);
            }
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
        $tableName = $firstItem instanceof Model
            ? $firstItem->getTable()
            : 'export';

        return "{$tableName}.{$extension}";
    }

    /**
     * Normalize a collection item to an array for export operations.
     */
    protected function itemToArray(mixed $item): array
    {
        if ($item instanceof Model) {
            return $item->toArray();
        }

        if (is_array($item)) {
            return $item;
        }

        if (is_object($item) && method_exists($item, 'toArray')) {
            /** @var array $array */
            $array = $item->toArray();

            return $array;
        }

        return (array) $item;
    }
}
