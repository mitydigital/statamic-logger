<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Nav;
use Statamic\Events\NavSaved;

it('returns the correct nav data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $nav = \Statamic\Facades\Nav::make()
        ->title('My User Role')
        ->handle('my_user_role');
    $nav->save();

    // create the event
    $event = new NavSaved($nav);

    // create the listener
    $listener = new Nav();
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($nav->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($nav->title());
});

it('returns the correct view', function () {
    $listener = new Nav();

    expect($listener->view())->toBe('statamic-logger::listeners.nav');
});
