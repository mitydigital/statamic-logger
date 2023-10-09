<?php

namespace MityDigital\StatamicLogger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool enabled()
 * @method static void subscribe($event, $handler)
 * @method static array getSubscribedEvents()
 * @method static void addTemporaryData(string $key, array $data)
 * @method static array|null getTemporaryData(string $key)
 * @method static string getRequestId()
 * @method static string getStoragePath()
 * @method static string getStorageFilename()
 *
 * @see \MityDigital\StatamicLogger\Support\StatamicLogger
 */
class StatamicLogger extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MityDigital\StatamicLogger\Support\StatamicLogger::class;
    }
}
