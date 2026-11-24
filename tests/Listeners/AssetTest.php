<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Asset;
use Statamic\Assets\AssetContainer;
use Statamic\Events\AssetReferencesUpdated;
use Statamic\Events\AssetReplaced;
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

it('returns the original asset when an asset is replaced', function (bool $deleteOriginal) {
    // disable actual events
    Event::fake();

    // supporting components
    $assetContainer = (new AssetContainer)
        ->title('Test Container')
        ->handle('test_container')
        ->disk('assets')
        ->save();

    $upload = function (string $filename) use ($assetContainer) {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_asset');
        copy(__DIR__.'/../__fixtures__/assets/mity.png', $tmpFile);

        return $assetContainer
            ->makeAsset($filename)
            ->upload(new UploadedFile($tmpFile, $filename, 'image/png', null, true));
    };

    $originalAsset = $upload('original.png');
    $newAsset = $upload('new.png');

    // replace the asset, and get the event that Statamic dispatches
    $newAsset->replace($originalAsset, $deleteOriginal);
    $event = Event::dispatched(AssetReplaced::class)->sole()[0];

    // create the listener
    $listener = new Asset;
    $data = getEventHandlerData($listener, $event);

    expect($data)->toBe([
        'id' => 'test_container::original.png',
        'name' => 'original.png',
        'container' => [
            'id' => 'test_container',
            'name' => 'Test Container',
        ],
    ]);
})->with([
    'original is kept' => false,
    'original is deleted' => true,
]);

it('logs asset references being updated', function () {
    // disable actual events
    Event::fake();

    // supporting components
    $assetContainer = (new AssetContainer)
        ->title('Test Container')
        ->handle('test_container')
        ->disk('assets')
        ->save();

    $assetContainer->disk()->put('mity.png', file_get_contents(__DIR__.'/../__fixtures__/assets/mity.png'));
    $asset = $assetContainer->makeAsset('mity.png');

    $event = new AssetReferencesUpdated($asset);

    expect(getEventHandlerData(new Asset, $event))->toBe([
        'id' => 'test_container::mity.png',
        'name' => 'mity.png',
        'container' => [
            'id' => 'test_container',
            'name' => 'Test Container',
        ],
    ])
        ->and(renderLoggedEvent(new Asset, $event))->toContain("Updated references to 'mity.png' in the 'Test Container' Container");
});

it('returns the correct view', function () {
    $listener = new Asset;

    expect($listener->view())->toBe('statamic-logger::listeners.asset');
});
