<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Is logging enabled?
    |--------------------------------------------------------------------------
    |
    | When enabled, Logger for Statamic log files will be created for all of
    | the configured events. This will also enable the Utility within the
    | Statamic CP.
    |
    */

    'enabled' => env('LOGGER_FOR_STATAMIC_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Excluded events
    |--------------------------------------------------------------------------
    |
    | Logger for Statamic is logging heaps of events out of the box - and you
    | may want to exclude some of them. You can do this by adding the full
    | class name here, such as:
    |   \Statamic\Events\EntryCreated::class
    |
    | To see what Logger for Statamic is listening to, run this command:
    |   php artisan statamic-logger:list
    |
    */

    'exclude' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | Have an opinion on where Logger's logs are stored, what they're called
    | or how long they're kept? Let your voice be heard!
    |
    */

    'storage' => [

        /*
        |--------------------------------------------------------------------------
        | Storage path
        |--------------------------------------------------------------------------
        |
        | Logs will be stored within Laravel's "log" folder. Makes sense, right?
        | If you want to change this, you can place a new path here. It will
        | always be run through the storage_path helper first.
        |
        */

        'path' => env('LOGGER_FOR_STATAMIC_STORAGE_PATH', null),

        /*
        |--------------------------------------------------------------------------
        | Storage file name
        |--------------------------------------------------------------------------
        |
        | Logs will be stored with a special filename just for you. You probably
        | won't need to change this, but if you want to, go for it.
        |
        */

        'name' => env('LOGGER_FOR_STATAMIC_STORAGE_NAME', 'statamic-logger'),

        /*
        |--------------------------------------------------------------------------
        | Retention
        |--------------------------------------------------------------------------
        |
        | Under the hood, Logger for Statamic uses Laravel (and in turn Monolog)
        | for logging, and is a "daily" channel - meaning a new log is created
        | each day, up to a set limit. After this, older log files will be
        | automatically removed.
        |
        */

        'retention' => env('LOGGER_FOR_STATAMIC_STORAGE_RETENTION_DAYS', 7),

    ],
];
