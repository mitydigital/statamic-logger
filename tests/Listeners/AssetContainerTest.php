<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\AssetContainer;
use Statamic\Events\AssetContainerSaved;

it('returns the correct asset container data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $assetContainer = (new \Statamic\Assets\AssetContainer())
        ->title('Test Container')
        ->handle('test_container')
        ->disk('assets')
        ->save();

    // create the event
    $event = new AssetContainerSaved($assetContainer);

    // create the listener
    $listener = new AssetContainer();
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($assetContainer->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($assetContainer->title());
});

it('returns the correct view', function () {
    $listener = new AssetContainer();

    expect($listener->view())->toBe('statamic-logger::listeners.asset-container');
});
