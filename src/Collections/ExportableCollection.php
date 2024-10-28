<?php

namespace PeterSowah\LaravelFactoryDumps\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
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
     * Export the collection to a CSV file.
     *
     * @throws UnavailableStream
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function toCsv(?string $fileName = null): string
    {
        // Define the default file name if not provided
        $fileName = $fileName ?? ($this->first()->getTable().'.csv');

        // Use database_path() for a better path structure
        $filePath = database_path("dumps/csv/{$fileName}");

        // Ensure the directory exists before creating the file
        File::ensureDirectoryExists(database_path('dumps/csv'));

        // Create CSV Writer and write headers and content
        $csv = Writer::createFromPath($filePath, 'w+');
        $csv->insertOne(array_keys($this->first()->toArray())); // Insert headers

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
        $fileName = $fileName ?? ($this->first()->getTable().'.xlsx');

        $relativePath = 'dumps/excel';
        $fullPath = database_path($relativePath);

        if (! File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }

        $databaseDisk = [
            'driver' => 'local',
            'root' => database_path(),
        ];

        Config::set('filesystems.disks.database', $databaseDisk);

        Excel::store(
            new ExportFactory($this->toArray()),
            "{$relativePath}/{$fileName}",
            'database'
        );

        return database_path("{$relativePath}/{$fileName}");
    }
}
