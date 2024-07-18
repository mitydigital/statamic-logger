<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\SiteCreated;
use Statamic\Events\SiteDeleted;
use Statamic\Events\SiteSaved;

class Site extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.site';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->site->handle(),
            'name' => $event->site->name(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            SiteCreated::class => __('statamic-logger::verbs.created'),
            SiteDeleted::class => __('statamic-logger::verbs.deleted'),
            SiteSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
