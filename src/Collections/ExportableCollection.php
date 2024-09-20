<?php

namespace PeterSowah\LaravelFactoryDumps\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use PeterSowah\LaravelFactoryDumps\Exports\ExportFactory;

class ExportableCollection extends Collection
{
    /**
     * @throws UnavailableStream
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function toCsv($fileName = null): string
    {
        $fileName = $fileName ?? ($this->first()->getTable().'.csv');
        $filePath = config('factory-dumps.path')."/csv/{$fileName}";

        File::ensureDirectoryExists(config('factory-dumps.path').'/csv');

        $csv = Writer::createFromPath($filePath, 'w+');
        $csv->insertOne(array_keys($this->first()->toArray())); // headers
        foreach ($this->toArray() as $row) {
            $csv->insertOne($row);
        }

        return $filePath;
    }

    public function toExcel($fileName = null): string
    {
        $fileName = $fileName ?? ($this->first()->getTable().'.xlsx');
        $filePath = config('factory-dumps.path')."/excel/{$fileName}";

        File::ensureDirectoryExists(config('factory-dumps.path').'/excel');

        Excel::store(new ExportFactory($this->toArray()), $fileName, null, ExcelType::XLSX);

        return $filePath;
    }
}
