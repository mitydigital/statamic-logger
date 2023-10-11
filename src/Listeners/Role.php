<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\RoleDeleted;
use Statamic\Events\RoleSaved;

class Role extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.role';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->role->handle(),
            'name' => $event->role->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            RoleDeleted::class => __('statamic-logger::verbs.deleted'),
            RoleSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
