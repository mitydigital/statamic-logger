<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\LocalizedTermDeleted;
use Statamic\Events\LocalizedTermSaved;

class LocalizedTerm extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.localized-term';
    }

    protected function data($event): array
    {
        $site = \Statamic\Facades\Site::get($event->term->locale());

        return [
            'id' => $event->term->id(),
            'name' => $event->term->title(),
            'site' => [
                'id' => $site->handle(),
                'name' => $site->name(),
            ],
            'taxonomy' => [
                'id' => $event->term->taxonomy()->handle(),
                'name' => $event->term->taxonomy()->title(),
            ],
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            LocalizedTermDeleted::class => __('statamic-logger::verbs.deleted'),
            LocalizedTermSaved::class => __('statamic-logger::verbs.saved'),
        };
    }
}
