<?php

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;
use MityDigital\StatamicLogger\Abstracts\EventListener;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use Statamic\Facades\User;

use function Pest\Laravel\actingAs;

it('has the public handle method which calls the log method', function () {
    expect(collect((new ReflectionClass(EventListener::class))
        ->getMethods(ReflectionMethod::IS_PUBLIC))
        ->pluck('name')
        ->search('handle'))
        ->not()->toBeFalse();
});

it('has the protected build log entry method', function () {
    expect(collect((new ReflectionClass(EventListener::class))
        ->getMethods(ReflectionMethod::IS_PROTECTED))
        ->pluck('name')
        ->search('buildLogEntry'))
        ->not()->toBeFalse();
});

it('returns the expected log structure from the build log entry method', function () {
    // request
    // event
    // handler
    // data
    // user

    // fake a verified event (laravel auth)
    $event = new Verified('user');

    // build a custom handler
    $handler = new class extends EventListener
    {
        protected function data($event): array
        {
            return [
                'id' => 1,
                'name' => 'name',
            ];
        }

        public function view(): string
        {
            return 'verified-view';
        }

        protected function verb(mixed $event): string
        {
            return 'verb';
        }
    };
    $data = callProtectedMethod($handler, 'buildLogEntry', [$event]);

    // there should be no user (null) because no one is logged in
    expect($data)->toHaveCount(5)->toHaveKeys(['data', 'event', 'handler', 'request', 'user'])
        ->and($data['request'])->toBe(StatamicLogger::getRequestId())
        ->and($data['handler'])->toBe(get_class($handler))
        ->and($data['event'])->toBe(get_class($event))
        ->and($data['data'])->toBeArray()
        ->and($data['data'])->toHaveCount(2)->toHaveKeys(['id', 'name'])
        ->and($data['user'])->toBeNull();

    // log in
    $user = User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();
    actingAs($user);

    $data = callProtectedMethod($handler, 'buildLogEntry', [$event]);

    // there should be no user (null) because no one is logged in
    expect($data)->toHaveCount(5)->toHaveKeys(['data', 'event', 'handler', 'request', 'user'])
        ->and($data['request'])->toBe(StatamicLogger::getRequestId())
        ->and($data['handler'])->toBe(get_class($handler))
        ->and($data['event'])->toBe(get_class($event))
        ->and($data['data'])->toBeArray()
        ->and($data['data'])->toHaveCount(2)->toHaveKeys(['id', 'name'])
        ->and($data['user'])->toBeArray()
        ->and($data['user'])->toHaveKeys(['id', 'name', 'model'])
        ->and($data['user']['id'])->toBe($user->id)
        ->and($data['user']['name'])->toBe($user->name())
        ->and($data['user']['model'])->toBe(get_class($user));

});

it('returns the handlers data in the build log entry method', function () {
    // fake a verified event (laravel auth)
    $event = new Verified('user');

    // build a custom handler
    $handler = new class extends EventListener
    {
        protected function data($event): array
        {
            return [
                'id' => 'my-id',
                'name' => 'name',
                'value' => 'foo bar',
            ];
        }

        public function view(): string
        {
            return 'verified-view';
        }

        protected function verb(mixed $event): string
        {
            return 'verb';
        }
    };

    $data = callProtectedMethod($handler, 'buildLogEntry', [$event])['data'];

    expect($data)->toHaveCount(3)->toHaveKeys(['id', 'name', 'value'])
        ->and($data['id'])->toBe('my-id')
        ->and($data['name'])->toBe('name')
        ->and($data['value'])->toBe('foo bar');
});

it('supplements additional data in the build log entry method', function () {
    // fake a verified event (laravel auth)
    $event = new Verified('user');

    // build a custom handler
    $handler = new class extends EventListener
    {
        protected function data($event): array
        {
            return [
                'id' => 1,
                'name' => 'name',
            ];
        }

        protected function supplement($event): array
        {
            return [
                'supplement' => 'abc',
                'more' => '123',
            ];
        }

        public function view(): string
        {
            return 'string';
        }

        protected function verb(mixed $event): string
        {
            return 'verb';
        }
    };
    $data = callProtectedMethod($handler, 'buildLogEntry', [$event])['data'];

    expect($data)
        ->toHaveCount(4)
        // id
        ->and($data)->toHaveKey('id')
        ->and($data['id'])->toBe(1)
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe('name')
        // supplement
        ->and($data)->toHaveKey('supplement')
        ->and($data['supplement'])->toBe('abc')
        // more
        ->and($data)->toHaveKey('more')
        ->and($data['more'])->toBe('123');
});

it('writes to the log with the handle method', function () {
    // we expect the Log facade to be called once
    Log::shouldReceive('channel->info')->once();

    // fake a verified event (laravel auth)
    $event = new Verified('user');

    $handler = new class extends EventListener
    {
        protected function data($event): array
        {
            return ['id' => 1];
        }

        public function view(): string
        {
            return 'string';
        }

        protected function verb(mixed $event): string
        {
            return 'verb';
        }
    };
    $handler->handle($event);
});

it('defines data as an abstract method', function () {
    expect(collect((new ReflectionClass(EventListener::class))
        ->getMethods(ReflectionMethod::IS_ABSTRACT))
        ->pluck('name')
        ->search('data'))
        ->not()->toBeFalse();
});

it('defines view as an abstract method', function () {
    expect(collect((new ReflectionClass(EventListener::class))
        ->getMethods(ReflectionMethod::IS_ABSTRACT))
        ->pluck('name')
        ->search('view'))
        ->not()->toBeFalse();
});
