<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\AddonSettings;
use Statamic\Events\AddonSettingsSaved;
use Statamic\Facades\Addon;

it('returns the correct entry data structure', function () {
    // disable actual events
    Event::fake();

    $addon = Addon::get('mitydigital/statamic-logger');

    // create the event
    $event = new AddonSettingsSaved($addon->settings());

    // create the listener
    $listener = new AddonSettings;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($addon->id())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($addon->name());
});

it('returns the correct view', function () {
    $listener = new AddonSettings;

    expect($listener->view())->toBe('statamic-logger::listeners.addon-settings');
});
