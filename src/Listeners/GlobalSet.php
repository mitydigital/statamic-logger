<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\GlobalSetCreated;
use Statamic\Events\GlobalSetDeleted;
use Statamic\Events\GlobalSetSaved;

class GlobalSet extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.global-set';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->globals->handle(),
            'name' => $event->globals->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            GlobalSetCreated::class => __('statamic-logger::verbs.created'),
            GlobalSetDeleted::class => __('statamic-logger::verbs.deleted'),
            GlobalSetSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
