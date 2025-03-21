<?php

// config for PeterSowah/LaravelFactoryDumps
return [
    'path' => config('app.env') === 'testing'
        ? __DIR__.'/../workbench/database/dumps'
        : storage_path('app/dumps'),
];
