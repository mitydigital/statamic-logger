<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Utility;
use Statamic\Events\StacheWarmed;

it('returns the correct user data structure', function () {
    // disable actual events
    Event::fake();

    // create the event
    $event = new StacheWarmed;

    // create the listener
    $listener = new Utility;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(1)
        ->toHaveKey('utility');
});

it('returns the correct view', function () {
    $listener = new Utility;

    expect($listener->view())->toBe('statamic-logger::listeners.utility');
});
