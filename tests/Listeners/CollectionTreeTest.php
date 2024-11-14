<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\CollectionTree;
use Statamic\Entries\Collection;
use Statamic\Entries\Entry;
use Statamic\Events\CollectionTreeSaved;
use Statamic\Structures\CollectionStructure;

it('returns the correct collection tree data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $collection = (new Collection)
        ->title('Blog Posts')
        ->handle('blog')
        ->sites(['default'])
        ->save();

    $structure = (new CollectionStructure)
        ->maxDepth(1)
        ->showSlugs(true);
    $collection->structure($structure);

    $entry = Entry::make()
        ->collection($collection)
        ->slug('entry')
        ->data([])
        ->published(Carbon::now());
    $entry->save();

    $tree = $collection->structure()->in('default');
    $contents = $collection->structure()->validateTree([
        [
            'entry' => $entry->id(),
            'title' => $entry->title,
            'url' => $entry->url(),
            'children' => [],
        ],
    ], 'default');
    $tree->tree($contents)->save();

    // create the event
    $event = new CollectionTreeSaved($tree);

    // create the listener
    $listener = new CollectionTree;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(3)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($collection->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($collection->title())
        // site
        ->and($data)->toHaveKey('site')
        ->and($data['site'])->toBe('default');
});

it('returns the correct view', function () {
    $listener = new CollectionTree;

    expect($listener->view())->toBe('statamic-logger::listeners.collection-tree');
});
