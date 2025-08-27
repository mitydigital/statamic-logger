<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\TwoFactor;
use Statamic\Events\TwoFactorAuthenticationEnabled;

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
    $event = new TwoFactorAuthenticationEnabled($user);

    // create the listener
    $listener = new TwoFactor;
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
    $listener = new TwoFactor;

    expect($listener->view())->toBe('statamic-logger::listeners.two-factor');
});
