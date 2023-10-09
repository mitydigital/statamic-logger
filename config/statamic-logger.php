<?php

return [

    'enabled' => env('LOGGER_ENABLED', true),

    'exclude' => [

    ],

    'storage' => [

        'path' => env('LOGGER_STORAGE_PATH', null),

        'name' => env('LOGGER_STORAGE_NAME', 'statamic-logger'),

        'retention' => env('LOGGER_STORAGE_RETENTION_DAYS', 7),

    ],
];
