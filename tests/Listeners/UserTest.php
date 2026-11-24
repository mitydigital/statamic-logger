<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\User;
use Statamic\Events\UserRegistered;
use Statamic\Events\UserSaved;

it('returns the correct user data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $user = Statamic\Facades\User::make()
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

it('logs a user registering', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $user = Statamic\Facades\User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    $event = new UserRegistered($user);

    expect(getEventHandlerData(new User, $event))->toBe([
        'id' => $user->id(),
        'name' => 'Peter Parker',
    ])
        ->and(renderLoggedEvent(new User, $event))->toContain('Peter Parker registered');
});

it('logs a completed password reset', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $user = Statamic\Facades\User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    expect(renderLoggedEvent(new User, new PasswordReset($user)))
        ->toContain('Peter Parker reset their password');
});

it('returns the correct view', function () {
    $listener = new User;

    expect($listener->view())->toBe('statamic-logger::listeners.user');
});
