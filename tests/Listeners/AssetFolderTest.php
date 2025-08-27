<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\AssetFolder;
use Statamic\Assets\AssetContainer;
use Statamic\Events\AssetFolderSaved;

it('returns the correct asset data structure', function () {
    // disable actual events
    Event::fake();

    // supporting components
    $assetContainer = (new AssetContainer)
        ->title('Test Container')
        ->handle('test_container')
        ->disk('assets')
        ->save();

    $folder = $assetContainer->assetFolder('new-folder');

    // create the event
    $event = new AssetFolderSaved($folder);

    // create the listener
    $listener = new AssetFolder;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(3)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($folder->path())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($folder->title())
        // container
        ->and($data)->toHaveKey('container')
        // container - id
        ->and($data['container'])->toHaveKey('id')
        ->and($data['container']['id'])->toBe($assetContainer->handle())
        // container - name
        ->and($data['container'])->toHaveKey('name')
        ->and($data['container']['name'])->toBe($assetContainer->title());

    // make a nested folder
    $nestedFolder = $assetContainer->assetFolder('new-folder/nested');

    // create the event
    $event = new AssetFolderSaved($nestedFolder);

    // create the listener
    $listener = new AssetFolder;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(3)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($nestedFolder->path())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($nestedFolder->title())
        // container
        ->and($data)->toHaveKey('container')
        // container - id
        ->and($data['container'])->toHaveKey('id')
        ->and($data['container']['id'])->toBe($assetContainer->handle())
        // container - name
        ->and($data['container'])->toHaveKey('name')
        ->and($data['container']['name'])->toBe($assetContainer->title());
});

it('returns the correct view', function () {
    $listener = new AssetFolder;

    expect($listener->view())->toBe('statamic-logger::listeners.asset-folder');
});
