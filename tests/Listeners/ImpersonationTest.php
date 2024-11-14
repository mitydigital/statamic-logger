<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Impersonation;
use Statamic\Events\ImpersonationStarted;
use Statamic\Facades\User;

it('returns the correct user data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $impersonator = User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    $impersonated = User::make()
        ->makeSuper()
        ->set('name', 'Andrew Garfield')
        ->email('andrew.garfield@spiderman.com')
        ->set('password', 'secret')
        ->save();

    // create the event
    $event = new ImpersonationStarted($impersonator, $impersonated);

    // create the listener
    $listener = new Impersonation;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        ->toHaveKey('impersonator')
        ->toHaveKey('impersonated')
        // impersonator
        ->and($data['impersonator'])
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['impersonator']['id'])->toBe($impersonator->id())
        // name
        ->and($data['impersonator'])->toHaveKey('name')
        ->and($data['impersonator']['name'])->toBe($impersonator->name())
        // impersonated
        ->and($data['impersonated'])
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['impersonated']['id'])->toBe($impersonated->id())
        // name
        ->and($data['impersonated'])->toHaveKey('name')
        ->and($data['impersonated']['name'])->toBe($impersonated->name());

});

it('returns the correct view', function () {
    $listener = new Impersonation;

    expect($listener->view())->toBe('statamic-logger::listeners.impersonation');
});
