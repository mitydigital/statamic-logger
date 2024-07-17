<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Site;
use Statamic\Events\SiteSaved;

it('returns the correct user data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $site = \Statamic\Facades\Site::current();

    // create the event
    $event = new SiteSaved($site);

    // create the listener
    $listener = new Site();
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // handle
        ->toHaveKey('id')
        ->and($data['id'])->toBe($site->handle)
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($site->name());
});

it('returns the correct view', function () {
    $listener = new Site();

    expect($listener->view())->toBe('statamic-logger::listeners.site');
});
