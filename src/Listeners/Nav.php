<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\NavDeleted;
use Statamic\Events\NavSaved;

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
            NavDeleted::class => __('statamic-logger::verbs.deleted'),
            NavSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
