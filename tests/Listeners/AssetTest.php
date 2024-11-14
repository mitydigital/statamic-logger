<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Asset;
use Statamic\Assets\AssetContainer;
use Statamic\Events\AssetSaved;

it('returns the correct asset data structure', function () {
    // disable actual events
    Event::fake();

    // supporting components
    $assetContainer = (new AssetContainer)
        ->title('Test Container')
        ->handle('test_container')
        ->disk('assets')
        ->save();

    $tmpFile = tempnam(sys_get_temp_dir(), 'test_asset.png');
    copy(__DIR__.'/../__fixtures__/assets/mity.png', $tmpFile);

    $file = new UploadedFile(
        $tmpFile,
        'mity.png',
        'image/jpeg',
        null,
        true
    );

    $asset = $assetContainer->makeAsset($file->getFilename())->upload($file);

    // create the event
    $event = new AssetSaved($asset);

    // create the listener
    $listener = new Asset;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(3)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe('test_container::test_asset.png')
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe('test_asset.png')
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
    $listener = new Asset;

    expect($listener->view())->toBe('statamic-logger::listeners.asset');
});
