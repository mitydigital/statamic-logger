<?php

use MityDigital\StatamicLogger\Facades\StatamicLogger;
use MityDigital\StatamicLogger\ServiceProvider;

it('only defines the channel when enabled', function () {
    expect(StatamicLogger::enabled())->toBeTrue()
        ->and(config('logging.channels'))->toHaveKey('statamic-logger')
        // driver
        ->and(config('logging.channels.statamic-logger'))->toHaveKey('driver')
        ->and(config('logging.channels.statamic-logger.driver'))->toBe('daily')
        // path
        ->and(config('logging.channels.statamic-logger'))->toHaveKey('path')
        ->and(config('logging.channels.statamic-logger.path'))->toBe(storage_path('logs/statamic-logger.log'))
        // level
        ->and(config('logging.channels.statamic-logger'))->toHaveKey('level')
        ->and(config('logging.channels.statamic-logger.level'))->toBe('debug')
        // days
        ->and(config('logging.channels.statamic-logger'))->toHaveKey('days')
        ->and(config('logging.channels.statamic-logger.days'))->toBe(config('statamic-logger.storage.retention', 7));

    // disable and re-run
    config()->set('statamic-logger.enabled', false);
    $provider = new ServiceProvider(app());
    callProtectedMethod($provider, 'configureLogger', ['disabled-logger']);

    // expect disabled-logger to not exist
    expect(config('logging.channels'))->not()->toHaveKey('disabled-logger');
});

it('allows the path to be changed to a custom path', function () {
    // the custom path
    $path = 'custom-path';

    config()->set('statamic-logger.storage.path', $path);
    $provider = new ServiceProvider(app());
    callProtectedMethod($provider, 'configureLogger');

    expect(config('logging.channels.statamic-logger.path'))->toBe(storage_path($path.'/statamic-logger.log'));
});

it('allows the name to be changed to a custom name', function () {
    // custom name
    $name = 'custom-name';

    config()->set('statamic-logger.storage.name', $name);
    $provider = new ServiceProvider(app());
    callProtectedMethod($provider, 'configureLogger');

    expect(config('logging.channels.statamic-logger.path'))->toBe(storage_path('logs/'.$name.'.log'));
});

it('allows the path and name to be changed to a custom name', function () {
    // custom name
    $name = 'custom-name';

    // custom path
    $path = 'custom-path';

    config()->set('statamic-logger.storage.name', $name);
    config()->set('statamic-logger.storage.path', $path);

    $provider = new ServiceProvider(app());
    callProtectedMethod($provider, 'configureLogger');

    expect(config('logging.channels.statamic-logger.path'))->toBe(storage_path($path.'/'.$name.'.log'));
});

it('allows the retention period to be changed', function () {
    // custom days
    $days = 365;

    config()->set('statamic-logger.storage.retention', $days);

    $provider = new ServiceProvider(app());
    callProtectedMethod($provider, 'configureLogger');

    expect(config('logging.channels.statamic-logger.days'))->toBe($days);
});
