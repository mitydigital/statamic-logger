<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\UserGroup;
use Statamic\Events\UserGroupSaved;

it('returns the correct user group data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $userGroup = \Statamic\Facades\UserGroup::make()
        ->title('My User Group')
        ->data([])
        ->handle('my_user_group');
    $userGroup->save();

    // create the event
    $event = new UserGroupSaved($userGroup);

    // create the listener
    $listener = new UserGroup;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($userGroup->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($userGroup->title());
});

it('returns the correct view', function () {
    $listener = new UserGroup;

    expect($listener->view())->toBe('statamic-logger::listeners.user-group');
});
