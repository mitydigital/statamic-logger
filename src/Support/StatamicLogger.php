<?php

namespace MityDigital\StatamicLogger\Support;

use Illuminate\Support\Str;

class StatamicLogger
{
    protected string $requestId;

    protected array $subscribedEvents = [];

    protected array $temporaryData = [];

    public function __construct()
    {
        // create a UUID for this request
        $this->requestId = (string) Str::uuid();
    }

    public function enabled(): bool
    {
        return config('statamic-logger.enabled', false);
    }

    public function subscribe($event, $handler): void
    {
        $this->subscribedEvents[$event] = $handler;
    }

    public function getSubscribedEvents(): array
    {
        return $this->subscribedEvents;
    }

    public function addTemporaryData(string $key, array $data): void
    {
        $this->temporaryData[$key] = $data;
    }

    public function getTemporaryData(string $key): ?array
    {
        if (array_key_exists($key, $this->temporaryData)) {
            return $this->temporaryData[$key];
        }

        return null;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function getStoragePath(): string
    {
        $path = 'logs';
        if (config('statamic-logger.storage.path')) {
            // change the channel's 'path'
            $path = config('statamic-logger.storage.path');
        }

        // if was set to null, force it back to logs
        if (! $path) {
            $path = 'logs';
        }

        return $path;
    }

    public function getStorageFilename(): string
    {
        $filename = config('statamic-logger.storage.name', 'statamic-logger');

        // if was set to null, force it to the default
        if (! $filename) {
            $filename = 'statamic-logger';
        }

        return $filename;
    }
}
