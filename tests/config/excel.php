<?php

return [
    'exports' => [
        'chunk_size' => 1000,
    ],

    'imports' => [
        'read_only' => true,
    ],

    'value_binder' => [
        'default' => Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    'cache' => [
        'enable' => true,
        'driver' => 'memory',
        'batch' => false,
        'ttl' => 600,
    ],

    'transactions' => [
        'handler' => 'db',
        'db_connection' => null,
    ],

    'temporary_files' => [
        'local_path' => storage_path('framework/laravel-excel'),
        'remote_disk' => null,
        'remote_prefix' => null,
        'force_resync_remote' => null,
    ],
]; 