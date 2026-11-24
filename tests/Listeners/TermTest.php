<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Term;
use Statamic\Events\TermReferencesUpdated;
use Statamic\Events\TermSaved;
use Statamic\Facades\Taxonomy;

it('returns the correct term data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $taxonomy = Taxonomy::make()
        ->title('My Taxonomy')
        ->handle('my_taxonomy');
    $taxonomy->save();

    $term = Statamic\Facades\Term::make()
        ->taxonomy($taxonomy)
        ->in('default')
        ->data([
            'title' => 'My Term',
        ]);
    $term->save();

    // create the event
    $event = new TermSaved($term);

    // create the listener
    $listener = new Term;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(3)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($term->id())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($term->title())
        // taxonomy
        ->and($data)->toHaveKey('taxonomy')
        // taxonomy - id
        ->and($data['taxonomy'])->toHaveKey('id')
        ->and($data['taxonomy']['id'])->toBe($taxonomy->handle())
        // taxonomy - name
        ->and($data['taxonomy'])->toHaveKey('name')
        ->and($data['taxonomy']['name'])->toBe($taxonomy->title());
});

it('logs term references being updated', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $taxonomy = Taxonomy::make()
        ->title('My Taxonomy')
        ->handle('my_taxonomy');
    $taxonomy->save();

    $term = Statamic\Facades\Term::make('my-term')->taxonomy($taxonomy);
    $term->in('default')->data(['title' => 'My Term']);
    $term->save();

    $event = new TermReferencesUpdated($term);

    expect(getEventHandlerData(new Term, $event))->toBe([
        'id' => 'my_taxonomy::my-term',
        'name' => 'My Term',
        'taxonomy' => [
            'id' => 'my_taxonomy',
            'name' => 'My Taxonomy',
        ],
    ])
        ->and(renderLoggedEvent(new Term, $event))->toContain("Updated references to 'My Term' in the 'My Taxonomy' Taxonomy");
});

it('returns the correct view', function () {
    $listener = new Term;

    expect($listener->view())->toBe('statamic-logger::listeners.term');
});
