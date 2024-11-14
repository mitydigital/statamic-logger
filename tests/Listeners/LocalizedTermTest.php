<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\LocalizedTerm;
use Statamic\Events\LocalizedTermSaved;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy;
use Statamic\Facades\Term;

it('returns the correct term data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $taxonomy = Taxonomy::make()
        ->title('My Taxonomy')
        ->handle('my_taxonomy');
    $taxonomy->save();

    $site = Site::current();

    $term = Term::make()
        ->taxonomy($taxonomy)
        ->in('default')
        ->data([
            'title' => 'My Term',
        ]);
    $term->save();

    // create the event
    $event = new LocalizedTermSaved($term);

    // create the listener
    $listener = new LocalizedTerm;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(4)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($term->id())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($term->title())
        // site
        ->and($data)->toHaveKey('site')
        // site - id
        ->and($data['site'])->toHaveKey('id')
        ->and($data['site']['id'])->toBe($site->handle())
        // site - name
        ->and($data['site'])->toHaveKey('name')
        ->and($data['site']['name'])->toBe($site->name())
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
    $listener = new LocalizedTerm;

    expect($listener->view())->toBe('statamic-logger::listeners.localized-term');
});
