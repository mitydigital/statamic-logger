<?php

it('is enabled by default', function () {
    expect(config('statamic-logger.enabled', 'default-enabled'))->toBeTrue();
});

it('has an empty array for excluded', function () {
    expect(config('statamic-logger.exclude', 'default-exclude'))->toBeArray()->toHaveCount(0);
});

it('has no storage path', function () {
    expect(config('statamic-logger.storage.path', 'default-path'))->toBeNull();
});

it('has a storage name', function () {
    expect(config('statamic-logger.storage.name', 'default-path'))->not()->toBeNull();
});

it('has a default retention period of 7 days', function () {
    expect(config('statamic-logger.storage.retention', 'default-retention'))->toBe(7);
});
