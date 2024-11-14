<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Entry;
use Statamic\Entries\Collection;
use Statamic\Events\EntrySaved;

it('returns the correct entry data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $collection = (new Collection)
        ->title('Blog Posts')
        ->handle('blog')
        ->sites(['default'])
        ->save();

    $entry = \Statamic\Facades\Entry::make()
        ->collection($collection)
        ->slug('entry')
        ->data([])
        ->published(Carbon::now());
    $entry->save();

    // create the event
    $event = new EntrySaved($entry);

    // create the listener
    $listener = new Entry;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(4)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($entry->id())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($entry->title)
        // collection
        ->and($data)->toHaveKey('collection')
        // collection - id
        ->and($data['collection'])->toHaveKey('id')
        ->and($data['collection']['id'])->toBe($collection->handle())
        // collection - name
        ->and($data['collection'])->toHaveKey('name')
        ->and($data['collection']['name'])->toBe($collection->title())
        // site
        ->and($data)->toHaveKey('site')
        ->and($data['site'])->toBe('default');
});

it('returns the correct view', function () {
    $listener = new Entry;

    expect($listener->view())->toBe('statamic-logger::listeners.entry');
});
