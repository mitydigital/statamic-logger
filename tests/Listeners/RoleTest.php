<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Role;
use Statamic\Events\RoleSaved;

it('returns the correct role data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $role = \Statamic\Facades\Role::make()
        ->title('My User Role')
        ->handle('my_user_role');
    $role->save();

    // create the event
    $event = new RoleSaved($role);

    // create the listener
    $listener = new Role;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($role->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($role->title());
});

it('returns the correct view', function () {
    $listener = new Role;

    expect($listener->view())->toBe('statamic-logger::listeners.role');
});
