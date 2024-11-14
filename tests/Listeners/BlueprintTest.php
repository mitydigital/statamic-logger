<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Blueprint;
use Statamic\Entries\Collection;
use Statamic\Events\BlueprintSaved;

it('returns the correct blueprint data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $collection = (new Collection)
        ->title('Blog Posts')
        ->handle('blog')
        ->save();

    // create a blueprint
    $blueprint = (new \Statamic\Fields\Blueprint)
        ->setHandle('blog')
        ->setNamespace('collections.blog')
        ->setContents([
            'title' => 'Blog Post',
            'tabs' => [
                'main' => [
                    'display' => __('Main'),
                    'fields' => [],
                ],
            ],
        ])
        ->save();

    // create the event
    $event = new BlueprintSaved($blueprint);

    // create the listener
    $listener = new Blueprint;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(3)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($blueprint->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($blueprint->title())
        // namespace
        ->and($data)->toHaveKey('namespace')
        ->and($data['namespace'])->toBe($blueprint->namespace());
});

it('returns the correct view', function () {
    $listener = new Blueprint;

    expect($listener->view())->toBe('statamic-logger::listeners.blueprint');
});
