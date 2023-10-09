<?php

use Illuminate\Http\UploadedFile;
use MityDigital\StatamicLogger\Http\Resources\LogResource;
use Statamic\Assets\AssetContainer;
use Statamic\Facades\User;

beforeEach(function () {
    // create a user
    $this->user = User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    // create a line
    $this->line = '[2023-10-09 14:05:06] production.INFO: {"request":"f63923d9-5197-4630-89ef-4bf0b4ed32a0","event":"Statamic\\\Events\\\EntrySaved","handler":"MityDigital\\\StatamicLogger\\\Listeners\\\Entry","user":{"id":"'.$this->user->id().'","name":"Marty","model":"Statamic\\\Auth\\\File\\\User"},"data":{"id":"bd88ae81-9988-4c32-a866-7bd223c30d5a","name":"Post 5","collection":{"id":"blog","name":"Blog"},"site":"default"}}';
});

it('correctly parses a monolog string', function () {
    // call the parsing method
    $resource = callProtectedMethod(new LogResource($this->line), 'parseLog');

    expect($resource)
        ->toBeArray()
        ->toHaveCount(4)
        ->toHaveKeys(['datetime', 'env', 'level', 'message']);
});

it('returns the correct response format', function () {
    $resource = new LogResource($this->line);

    // get the response
    $response = $resource->toArray(request());

    // parse the data
    $data = callProtectedMethod($resource, 'parseLog');

    expect($response)
        ->toBeArray()
        ->toHaveKeys(['date', 'user', 'type', 'detail'])
        // date
        ->and($response['date'])
        ->toBe($data['datetime'])
        // user
        ->and($response['user'])
        ->toBeArray()
        ->toHaveKeys(['id', 'name', 'initials', 'avatar'])
        // type
        ->and($response['type'])
        ->not()->toBeNull()
        // detail
        ->and($response['detail'])
        ->not()->toBeNull();
});

it('includes user details from the log, not the current user', function () {
    // build the resource
    $resource = new LogResource($this->line);

    // get the response
    $response = $resource->toArray(request());

    // name is different
    expect($response['user']['name'])
        ->toBe('Marty')
        ->not()->toBe($this->user->name())
        // id matches
        ->and($response['user']['id'])
        ->toBe($this->user->id());
});

it('returns null if no avatar is configured for the user', function () {
    // ensure we have no avatar
    expect($this->user->avatar())->toBeNull();

    // get the resource
    $response = (new LogResource($this->line))->toArray(request());

    // ensure there is no avatar
    expect($response['user']['avatar'])->toBeNull();
});

it('returns the avatar if one is configured', function () {
    // ensure we have no avatar
    expect($this->user->avatar())->toBeNull();

    // create asset container
    $assetContainer = (new AssetContainer())
        ->title('Test Container')
        ->handle('test_container')
        ->disk('assets')
        ->save();

    // add support for the avatar
    $blueprint = $this->user->blueprint();
    $contents = $blueprint->contents();
    $contents['tabs']['main']['sections'][0]['fields'][] = [
        'handle' => 'avatar',
        'field' => [
            'type' => 'assets',
            'container' => 'test_container',
            'max_files' => 1,
        ],
    ];
    $blueprint->setContents($contents);
    $blueprint->save();

    $tmpFile = tempnam(sys_get_temp_dir(), 'test_asset.png');
    copy(__DIR__.'/../../__fixtures__/assets/mity.png', $tmpFile);

    $file = new UploadedFile(
        $tmpFile,
        'mity.png',
        'image/jpeg',
        null,
        true
    );

    $asset = $assetContainer->makeAsset($file->getFilename())->upload($file);

    $this->user->set('avatar', $asset->path());
    $this->user->save();

    // get the resource
    $response = (new LogResource($this->line))->toArray(request());

    // ensure there is an avatar
    expect($response['user']['avatar'])
        ->not()->toBeNull()
        ->toEqual($this->user->avatar());
});

it('returns a gravatar url if configured', function () {
    // ensure we have no avatar
    expect($this->user->avatar())->toBeNull();

    // configure gravatar
    config()->set('statamic.users.avatars', 'gravatar');

    // ensure we have a gravatar url
    expect($this->user->avatar())->not()->toBeNull();

    // get the resource
    $response = (new LogResource($this->line))->toArray(request());

    // ensure there is an avatar
    expect($response['user']['avatar'])
        ->not()->toBeNull()
        ->toEqual($this->user->avatar());
});
