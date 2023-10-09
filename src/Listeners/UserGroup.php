<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventHandler;
use Statamic\Events\UserGroupDeleted;
use Statamic\Events\UserGroupSaved;

class UserGroup extends EventHandler
{
    public function view(): string
    {
        return 'statamic-logger::listeners.user-group';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->group->handle(),
            'name' => $event->group->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            UserGroupDeleted::class => __('statamic-logger::verbs.deleted'),
            UserGroupSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
