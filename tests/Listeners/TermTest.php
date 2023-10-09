<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Term;
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

    $term = \Statamic\Facades\Term::make()
        ->taxonomy($taxonomy)
        ->in('default')
        ->data([
            'title' => 'My Term',
        ]);
    $term->save();

    // create the event
    $event = new TermSaved($term);

    // create the listener
    $listener = new Term();
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

it('returns the correct view', function () {
    $listener = new Term();

    expect($listener->view())->toBe('statamic-logger::listeners.term');
});
