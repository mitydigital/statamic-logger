<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\GlobalSet;
use Statamic\Events\GlobalSetSaved;

it('returns the correct form data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $globalSet = (\Statamic\Facades\GlobalSet::make('settings'))
        ->title('Settings');
    $globalSet->save();

    // create the event
    $event = new GlobalSetSaved($globalSet);

    // create the listener
    $listener = new GlobalSet;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($globalSet->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($globalSet->title());
});

it('returns the correct view', function () {
    $listener = new GlobalSet;

    expect($listener->view())->toBe('statamic-logger::listeners.global-set');
});
