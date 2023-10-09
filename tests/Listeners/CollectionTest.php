<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Collection;
use Statamic\Events\CollectionSaved;

it('returns the correct collection data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $collection = (new \Statamic\Entries\Collection())
        ->title('Blog Posts')
        ->handle('blog')
        ->sites(['default'])
        ->save();

    // create the event
    $event = new CollectionSaved($collection);

    // create the listener
    $listener = new Collection();
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($collection->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($collection->title());
});

it('returns the correct view', function () {
    $listener = new Collection();

    expect($listener->view())->toBe('statamic-logger::listeners.collection');
});
