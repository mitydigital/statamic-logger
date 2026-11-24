<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Utility;
use Statamic\Events\DefaultPreferencesSaved;
use Statamic\Events\DuplicateIdRegenerated;
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

it('logs events that have no payload', function (string $event, string $utility, string $message) {
    expect(getEventHandlerData(new Utility, new $event))->toBe(['utility' => $utility])
        ->and(renderLoggedEvent(new Utility, new $event))->toContain($message);
})->with([
    'default preferences' => [DefaultPreferencesSaved::class, 'Default Preferences', 'Default Preferences saved'],
    'duplicate id' => [DuplicateIdRegenerated::class, 'Duplicate ID', 'Duplicate ID regenerated'],
]);

it('returns the correct view', function () {
    $listener = new Utility;

    expect($listener->view())->toBe('statamic-logger::listeners.utility');
});
