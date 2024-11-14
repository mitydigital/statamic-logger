<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\User;
use Statamic\Events\UserSaved;

it('returns the correct user data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $user = \Statamic\Facades\User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    // create the event
    $event = new UserSaved($user);

    // create the listener
    $listener = new User;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($user->id())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($user->name());
});

it('returns the correct view', function () {
    $listener = new User;

    expect($listener->view())->toBe('statamic-logger::listeners.user');
});
