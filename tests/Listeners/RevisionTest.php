<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Revision;
use Statamic\Entries\Collection;
use Statamic\Events\RevisionSaved;
use Statamic\Facades\Entry;
use Statamic\Facades\User;

it('returns the correct term data structure', function () {
    // disable actual events
    Event::fake();

    config()->set('statamic.revisions.enabled', true);

    // create supporting components
    $user = User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    $collection = (new Collection)
        ->title('Blog Posts')
        ->handle('blog')
        ->sites(['default'])
        ->revisionsEnabled(true)
        ->save();

    $entry = Entry::make()
        ->collection($collection)
        ->slug('entry')
        ->data([])
        ->published(Carbon::now());
    $entry->save();

    $revision = $entry->makeRevision();
    $revision->save();

    // create the event
    $event = new RevisionSaved($revision);

    // create the listener
    $listener = new Revision;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(4)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($revision->id())
        // entry
        ->and($data)->toHaveKey('entry')
        // entry - id
        ->and($data['entry'])->toHaveKey('id')
        ->and($data['entry']['id'])->toBe($entry->id())
        // entry - name
        ->and($data['entry'])->toHaveKey('name')
        ->and($data['entry']['name'])->toBe($entry->title)
        // site
        ->and($data)->toHaveKey('site')
        // site - id
        ->and($data['site'])->toHaveKey('id')
        ->and($data['site']['id'])->toBe($entry->site()->handle())
        // site - name
        ->and($data['site'])->toHaveKey('name')
        ->and($data['site']['name'])->toBe($entry->site()->name())
        // collection
        ->and($data)->toHaveKey('collection')
        // collection - id
        ->and($data['collection'])->toHaveKey('id')
        ->and($data['collection']['id'])->toBe($collection->handle())
        // collection - name
        ->and($data['collection'])->toHaveKey('name')
        ->and($data['collection']['name'])->toBe($collection->title());
});

it('returns the correct view', function () {
    $listener = new Revision;

    expect($listener->view())->toBe('statamic-logger::listeners.revision');
});
