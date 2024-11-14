<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\NavTree;
use Statamic\Events\NavTreeSaved;
use Statamic\Facades\Nav;

it('returns the correct nav tree data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $nav = Nav::make()
        ->title('My Nav')
        ->handle('my_nav');
    $nav->save();

    $tree = $nav->in('default')->tree([]);
    $tree->save();

    // create the event
    $event = new NavTreeSaved($tree);

    // create the listener
    $listener = new NavTree;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($nav->id())
        // site
        ->and($data)->toHaveKey('site')
        ->and($data['site'])->toBe('default');
});

it('returns the correct view', function () {
    $listener = new NavTree;

    expect($listener->view())->toBe('statamic-logger::listeners.nav-tree');
});
