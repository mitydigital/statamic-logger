<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventHandler;
use Statamic\Events\BlueprintCreated;
use Statamic\Events\BlueprintDeleted;
use Statamic\Events\BlueprintSaved;

class Blueprint extends EventHandler
{
    public function view(): string
    {
        return 'statamic-logger::listeners.blueprint';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->blueprint->handle,
            'name' => $event->blueprint->title(),
            'namespace' => $event->blueprint->namespace(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            BlueprintCreated::class => __('statamic-logger::verbs.created'),
            BlueprintDeleted::class => __('statamic-logger::verbs.deleted'),
            BlueprintSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
