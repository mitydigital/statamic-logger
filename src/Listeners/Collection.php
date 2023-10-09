<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventHandler;
use Statamic\Events\CollectionCreated;
use Statamic\Events\CollectionDeleted;
use Statamic\Events\CollectionSaved;

class Collection extends EventHandler
{
    public function view(): string
    {
        return 'statamic-logger::listeners.collection';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->collection->handle(),
            'name' => $event->collection->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            CollectionCreated::class => __('statamic-logger::verbs.created'),
            CollectionDeleted::class => __('statamic-logger::verbs.deleted'),
            CollectionSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
