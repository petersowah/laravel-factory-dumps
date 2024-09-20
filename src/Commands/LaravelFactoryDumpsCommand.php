<?php

namespace PeterSowah\LaravelFactoryDumps\Commands;

use Illuminate\Console\Command;

class LaravelFactoryDumpsCommand extends Command
{
    public $signature = 'laravel-factory-dumps';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
