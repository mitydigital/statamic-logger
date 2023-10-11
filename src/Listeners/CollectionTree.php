<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\CollectionTreeDeleted;
use Statamic\Events\CollectionTreeSaved;

class CollectionTree extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.collection-tree';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->tree->handle(),
            'name' => \Statamic\Facades\Collection::find($event->tree->handle())->title(),
            'site' => $event->tree->site()->handle(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            CollectionTreeDeleted::class => __('statamic-logger::verbs.deleted'),
            CollectionTreeSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
