<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\RevisionDeleted;
use Statamic\Events\RevisionSaved;

class Revision extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.revision';
    }

    protected function data($event): array
    {
        $entry = \Statamic\Facades\Entry::find($event->revision->attribute('id'));

        return [
            'id' => $event->revision->id(),
            'entry' => [
                'id' => $entry ? $entry->id() : null,
                'name' => $entry ? $entry->title : null,
            ],
            'collection' => [
                'id' => $entry ? $entry->collection->handle : null,
                'name' => $entry ? $entry->collection->title() : null,
            ],
            'site' => [
                'id' => $entry?->site() ? $entry->site()->handle() : null,
                'name' => $entry?->site() ? $entry->site()->name() : null,
            ],
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            RevisionDeleted::class => __('statamic-logger::verbs.deleted'),
            RevisionSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
