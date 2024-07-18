<?php

use Illuminate\Auth\Events\Failed as AuthFailed;
use Illuminate\Auth\Events\Login as AuthLogin;
use Illuminate\Auth\Events\Logout as AuthLogout;
use Illuminate\Auth\Events\PasswordReset as AuthPasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;
use MityDigital\StatamicLogger\Abstracts\EventListener;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use MityDigital\StatamicLogger\Listeners\Entry;
use MityDigital\StatamicLogger\Subscribers\StatamicLoggerEventSubscriber;
use Statamic\Events\AssetContainerCreated;
use Statamic\Events\AssetContainerDeleted;
use Statamic\Events\AssetContainerSaved;
use Statamic\Events\AssetDeleted;
use Statamic\Events\AssetReplaced;
use Statamic\Events\AssetReuploaded;
use Statamic\Events\AssetSaved;
use Statamic\Events\AssetUploaded;
use Statamic\Events\BlueprintCreated;
use Statamic\Events\BlueprintDeleted;
use Statamic\Events\BlueprintSaved;
use Statamic\Events\CollectionCreated;
use Statamic\Events\CollectionDeleted;
use Statamic\Events\CollectionSaved;
use Statamic\Events\CollectionTreeDeleted;
use Statamic\Events\CollectionTreeSaved;
use Statamic\Events\EntryCreated;
use Statamic\Events\EntryDeleted;
use Statamic\Events\EntrySaved;
use Statamic\Events\FieldsetCreated;
use Statamic\Events\FieldsetDeleted;
use Statamic\Events\FieldsetSaved;
use Statamic\Events\FormCreated;
use Statamic\Events\FormDeleted;
use Statamic\Events\FormSaved;
use Statamic\Events\GlideCacheCleared;
use Statamic\Events\GlobalSetCreated;
use Statamic\Events\GlobalSetDeleted;
use Statamic\Events\GlobalSetSaved;
use Statamic\Events\ImpersonationEnded;
use Statamic\Events\ImpersonationStarted;
use Statamic\Events\LicenseSet;
use Statamic\Events\LicensesRefreshed;
use Statamic\Events\NavDeleted;
use Statamic\Events\NavSaved;
use Statamic\Events\NavTreeDeleted;
use Statamic\Events\NavTreeSaved;
use Statamic\Events\RoleDeleted;
use Statamic\Events\RoleSaved;
use Statamic\Events\SearchIndexUpdated;
use Statamic\Events\SiteCreated;
use Statamic\Events\SiteDeleted;
use Statamic\Events\SiteSaved;
use Statamic\Events\StacheCleared;
use Statamic\Events\StacheWarmed;
use Statamic\Events\StaticCacheCleared;
use Statamic\Events\TaxonomyCreated;
use Statamic\Events\TaxonomyDeleted;
use Statamic\Events\TaxonomySaved;
use Statamic\Events\TermCreated;
use Statamic\Events\TermDeleted;
use Statamic\Events\TermSaved;
use Statamic\Events\UserCreated;
use Statamic\Events\UserDeleted;
use Statamic\Events\UserGroupDeleted;
use Statamic\Events\UserGroupSaved;
use Statamic\Events\UserPasswordChanged;
use Statamic\Events\UserSaved;

beforeEach(function () {
    $this->events = [
        AssetContainerCreated::class,
        AssetContainerDeleted::class,
        AssetContainerSaved::class,

        AssetDeleted::class,
        AssetReplaced::class,
        AssetReuploaded::class,
        AssetSaved::class,
        AssetUploaded::class,

        AuthLogin::class,
        AuthLogout::class,
        AuthFailed::class,
        AuthPasswordReset::class,

        BlueprintCreated::class,
        BlueprintDeleted::class,
        BlueprintSaved::class,

        CollectionCreated::class,
        CollectionDeleted::class,
        CollectionSaved::class,

        CollectionTreeDeleted::class,
        CollectionTreeSaved::class,

        EntryCreated::class,
        EntryDeleted::class,
        EntrySaved::class,

        FieldsetCreated::class,
        FieldsetDeleted::class,
        FieldsetSaved::class,

        FormCreated::class,
        FormDeleted::class,
        FormSaved::class,

        GlideCacheCleared::class,

        GlobalSetCreated::class,
        GlobalSetDeleted::class,
        GlobalSetSaved::class,

        ImpersonationEnded::class,
        ImpersonationStarted::class,

        LicenseSet::class,
        LicensesRefreshed::class,

        NavDeleted::class,
        NavSaved::class,

        NavTreeDeleted::class,
        NavTreeSaved::class,

        RoleDeleted::class,
        RoleSaved::class,

        SearchIndexUpdated::class,

        SiteCreated::class,
        SiteDeleted::class,
        SiteSaved::class,

        StacheCleared::class,
        StacheWarmed::class,

        StaticCacheCleared::class,

        TaxonomyCreated::class,
        TaxonomyDeleted::class,
        TaxonomySaved::class,

        TermCreated::class,
        TermDeleted::class,
        TermSaved::class,

        UserCreated::class,
        UserDeleted::class,
        UserSaved::class,

        UserPasswordChanged::class,

        UserGroupDeleted::class,
        UserGroupSaved::class,
    ];
});

it('tracks the correct events', function () {

    $subscriber = new StatamicLoggerEventSubscriber();
    $subscribed = $subscriber->subscribe(new Dispatcher());

    expect($subscribed)->toHaveCount(count($this->events));

    foreach ($this->events as $event) {
        expect($subscribed)
            ->toHaveKey($event);
    }
});

it('can add additional events', function () {
    // subscribe to Laravel's "Verified" (because it is NOT in the events)
    // make sure Verified isn't there
    expect($this->events)->not()->toHaveKey(Verified::class);

    // build the handler
    $handler = new class extends EventListener
    {
        protected function data($event): array
        {
            return [];
        }

        public function view(): string
        {
            return 'view';
        }

        protected function verb(mixed $event): string
        {
            return 'verb';
        }
    };

    StatamicLogger::subscribe(Verified::class, $handler);

    // do we now have Verified in the list?
    $subscriber = new StatamicLoggerEventSubscriber();
    expect($subscriber->subscribe(new Dispatcher()))
        ->toHaveKey(Verified::class);
});

it('can override existing events', function () {
    // create our own EntrySaved handler
    // make sure EntrySaved is meant to be there
    expect(EntrySaved::class)->toBeIn($this->events);

    $subscriber = new StatamicLoggerEventSubscriber();
    $subscribed = $subscriber->subscribe(new Dispatcher());
    expect($subscribed)->toHaveKey(EntrySaved::class)
        ->and($subscribed[EntrySaved::class])->toBe(Entry::class);

    // create a new handler that extends the existing Entry handler
    // build the handler
    $handler = new class extends Entry
    {
    };

    StatamicLogger::subscribe(EntrySaved::class, $handler);

    // re-check
    $subscriber = new StatamicLoggerEventSubscriber();
    $subscribed = $subscriber->subscribe(new Dispatcher());

    expect($subscribed)->toHaveKey(EntrySaved::class)
        ->and($subscribed[EntrySaved::class])->toBe($handler);
});

it('can remove existing events', function () {
    // get the subscriber
    $subscriber = new StatamicLoggerEventSubscriber();

    // remove EntrySaved
    config()->set('statamic-logger.exclude', [
        EntrySaved::class,
    ]);

    expect($subscriber->subscribe(new Dispatcher()))
        ->not()->toHaveKey(EntrySaved::class);
});

it('subscribes to events when enabled only', function () {
    // disable
    config()->set('statamic-logger.enabled', false);

    $subscriber = new StatamicLoggerEventSubscriber();
    expect($subscriber->subscribe(new Dispatcher()))->toHaveCount(0);

    // enable
    config()->set('statamic-logger.enabled', true);

    $subscriber = new StatamicLoggerEventSubscriber();
    expect($subscriber->subscribe(new Dispatcher()))->not()->toHaveCount(0);
});
