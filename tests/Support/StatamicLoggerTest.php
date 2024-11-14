<?php

use MityDigital\StatamicLogger\Support\StatamicLogger;

beforeEach(function () {
    $this->logger = app(StatamicLogger::class);
});

it('adds can new event and listener, and retrieve them', function () {
    // should be empty
    expect($this->logger->getSubscribedEvents())->toHaveCount(0);

    // add one
    $this->logger->subscribe('event', 'listener');

    // should be one
    expect($this->logger->getSubscribedEvents())->toHaveCount(1);

    // add another
    $this->logger->subscribe('another-event', 'listener');

    // should be two
    expect($this->logger->getSubscribedEvents())->toHaveCount(2);

    // replace one
    $this->logger->subscribe('another-event', 'listener');

    // should still be two
    expect($this->logger->getSubscribedEvents())->toHaveCount(2);
});

it('can add temporary data and return temporary data', function () {
    // add one
    $this->logger->addTemporaryData('key', ['a' => 1]);

    // should be one
    expect($this->logger->getTemporaryData('key'))->toBe(['a' => 1]);

    // add another
    $this->logger->addTemporaryData('another-key', ['b' => 2]);

    // should be two
    expect($this->logger->getTemporaryData('another-key'))->toBe(['b' => 2]);

    // replace one
    $this->logger->addTemporaryData('another-key', ['c' => 3]);

    // should still be two
    expect($this->logger->getTemporaryData('another-key'))->toBe(['c' => 3]);
});

it('returns null when temporary data was not found', function () {
    expect($this->logger->getTemporaryData('key'))->toBeNull();
});

it('returns the correct enabled state of statamic logger', function () {
    // enable
    config()->set('statamic-logger.enabled', true);
    expect($this->logger->enabled())->toBeTrue();

    // disable
    config()->set('statamic-logger.enabled', false);
    expect($this->logger->enabled())->toBeFalse();
});

it('returns a new uuid for each new instance of the statamic logger', function () {
    $uuid1 = (new StatamicLogger)->getRequestId();
    $uuid2 = (new StatamicLogger)->getRequestId();

    expect($uuid1)
        ->not()->toBeNull()
        ->not()->toEqual($uuid2);
});

it('returns the configured path', function () {
    // "logs" by default
    expect($this->logger->getStoragePath())->toBe('logs');

    // allow overriding
    config()->set('statamic-logger.storage.path', 'custom-path');

    // expect "custom-path"
    expect($this->logger->getStoragePath())->toBe('custom-path');
});

it('returns the configured filename', function () {
    // "logs" by default
    expect($this->logger->getStorageFilename())->toBe('statamic-logger');

    // allow overriding
    config()->set('statamic-logger.storage.name', 'custom-name');

    // expect "custom-name"
    expect($this->logger->getStorageFilename())->toBe('custom-name');
});
