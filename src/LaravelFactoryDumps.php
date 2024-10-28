<?php

namespace PeterSowah\LaravelFactoryDumps;

use Illuminate\Support\Collection;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use PeterSowah\LaravelFactoryDumps\Collections\ExportableCollection;

class LaravelFactoryDumps
{
    /**
     * Export the given collection of models to CSV.
     *
     * @param  null  $fileName
     *
     * @throws CannotInsertRecord
     * @throws Exception
     * @throws UnavailableStream
     */
    public static function toCsv(Collection $models, $fileName = null): string
    {
        if ($models instanceof ExportableCollection) {
            return $models->toCsv($fileName);
        }

        throw new \RuntimeException('Invalid collection type. Ensure your model uses ExportableCollection.');
    }

    /**
     * Export the given collection of models to Excel.
     *
     * @param  null  $fileName
     *
     * @throws \Exception
     */
    public static function toExcel(Collection $models, $fileName = null): string
    {
        if ($models instanceof ExportableCollection) {
            return $models->toExcel($fileName);
        }

        throw new \RuntimeException('Invalid collection type. Ensure your model uses ExportableCollection.');
    }
}
