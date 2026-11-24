<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\DefaultPreferencesSaved;
use Statamic\Events\DuplicateIdRegenerated;
use Statamic\Events\GlideCacheCleared;
use Statamic\Events\LicenseSet;
use Statamic\Events\LicensesRefreshed;
use Statamic\Events\SearchIndexUpdated;
use Statamic\Events\StacheCleared;
use Statamic\Events\StacheWarmed;
use Statamic\Events\StaticCacheCleared;
use Statamic\Support\Str;

class Utility extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.utility';
    }

    protected function data($event): array
    {
        return [
            'utility' => match (get_class($event)) {
                DefaultPreferencesSaved::class => __('statamic-logger::utilities.preferences'),
                DuplicateIdRegenerated::class => __('statamic-logger::utilities.duplicate_id'),
                GlideCacheCleared::class => __('statamic-logger::utilities.glide'),
                LicenseSet::class => __('statamic-logger::utilities.license'),
                LicensesRefreshed::class => Str::plural(__('statamic-logger::utilities.license')),
                SearchIndexUpdated::class => __('statamic-logger::utilities.search'),
                StacheCleared::class, StacheWarmed::class => __('statamic-logger::utilities.stache'),
                StaticCacheCleared::class => __('statamic-logger::utilities.static'),
            },
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            DefaultPreferencesSaved::class => __('statamic-logger::verbs.saved'),
            DuplicateIdRegenerated::class => __('statamic-logger::verbs.regenerated'),
            GlideCacheCleared::class, StacheCleared::class, StaticCacheCleared::class => __('statamic-logger::verbs.cleared'),
            LicenseSet::class => __('statamic-logger::verbs.set'),
            LicensesRefreshed::class => __('statamic-logger::verbs.refreshed'),
            StacheWarmed::class => __('statamic-logger::verbs.warmed'),
            SearchIndexUpdated::class => __('statamic-logger::verbs.updated'),
        };
    }
}
