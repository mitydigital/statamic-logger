<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventHandler;
use Statamic\Events\NavTreeDeleted;
use Statamic\Events\NavTreeSaved;

class NavTree extends EventHandler
{
    public function view(): string
    {
        return 'statamic-logger::listeners.nav-tree';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->tree->handle(),
            'site' => $event->tree->site()->handle(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            NavTreeDeleted::class => __('statamic-logger::verbs.deleted'),
            NavTreeSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
