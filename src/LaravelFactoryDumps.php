<?php

namespace PeterSowah\LaravelFactoryDumps;

use Illuminate\Support\Collection;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use PeterSowah\LaravelFactoryDumps\Collections\ExportableCollection;

class LaravelFactoryDumps {
    /**
     * Export the given collection of models to CSV.
     *
     * @param Collection $models
     * @param null $fileName
     * @return string
     * @throws CannotInsertRecord
     * @throws Exception
     * @throws UnavailableStream
     */
    public static function toCsv($models, $fileName = null): string
    {
        if ($models instanceof ExportableCollection) {
            return $models->toCsv($fileName);
        }

        throw new \RuntimeException("Invalid collection type. Ensure your model uses ExportableCollection.");
    }

    /**
     * Export the given collection of models to Excel.
     *
     * @param Collection $models
     * @param null $fileName
     * @return string
     * @throws \Exception
     */
    public static function toExcel($models, $fileName = null)
    {
        if ($models instanceof ExportableCollection) {
            return $models->toExcel($fileName);
        }

        throw new \RuntimeException("Invalid collection type. Ensure your model uses ExportableCollection.");
    }
}
