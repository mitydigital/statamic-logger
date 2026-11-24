<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\TwoFactor;
use Statamic\Events\TwoFactorAuthenticationEnabled;
use Statamic\Events\ValidTwoFactorAuthenticationCodeProvided;
use Statamic\Facades\User;

it('returns the correct user data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $user = User::make()
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

it('logs a passed two factor challenge', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $user = User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    $event = new ValidTwoFactorAuthenticationCodeProvided($user);

    expect(getEventHandlerData(new TwoFactor, $event))->toBe([
        'id' => $user->id(),
        'name' => 'Peter Parker',
    ])
        ->and(renderLoggedEvent(new TwoFactor, $event))->toContain('Peter Parker passed a Two Factor challenge');
});

it('returns the correct view', function () {
    $listener = new TwoFactor;

    expect($listener->view())->toBe('statamic-logger::listeners.two-factor');
});
