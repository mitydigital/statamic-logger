<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\EntryCreated;
use Statamic\Events\EntryDeleted;
use Statamic\Events\EntrySaved;

class Entry extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.entry';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->entry->id(),
            'name' => $event->entry->title,
            'collection' => [
                'id' => $event->entry->collection->handle,
                'name' => $event->entry->collection->title(),
            ],
            'site' => $event->entry->site()->handle(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            EntryCreated::class => __('statamic-logger::verbs.created'),
            EntryDeleted::class => __('statamic-logger::verbs.deleted'),
            EntrySaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
