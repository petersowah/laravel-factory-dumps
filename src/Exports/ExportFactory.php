<?php

namespace PeterSowah\LaravelFactoryDumps\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ExportFactory implements FromArray
{
    public function __construct(protected array $data) {}

    public function array(): array
    {
        return $this->data;
    }
}
