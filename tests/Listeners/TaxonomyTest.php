<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Taxonomy;
use Statamic\Events\TaxonomySaved;

it('returns the correct taxonomy data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $taxonomy = \Statamic\Facades\Taxonomy::make()
        ->title('My Taxonomy')
        ->handle('my_taxonomy');
    $taxonomy->save();

    // create the event
    $event = new TaxonomySaved($taxonomy);

    // create the listener
    $listener = new Taxonomy();
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($taxonomy->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($taxonomy->title());
});

it('returns the correct view', function () {
    $listener = new Taxonomy();

    expect($listener->view())->toBe('statamic-logger::listeners.taxonomy');
});
