<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\NavCreated;
use Statamic\Events\NavCreating;
use Statamic\Events\NavDeleted;
use Statamic\Events\NavSaved;
use Statamic\Events\NavSaving;

class Nav extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.nav';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->nav->handle(),
            'name' => $event->nav->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            NavCreated::class => __('statamic-logger::verbs.created'),
            NavCreating::class => __('statamic-logger::verbs.creating'),
            NavDeleted::class => __('statamic-logger::verbs.deleted'),
            NavSaved::class => __('statamic-logger::verbs.saved'),
            NavSaving::class => __('statamic-logger::verbs.saving'),
        };
    }
}
