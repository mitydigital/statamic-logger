<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\GlobalVariablesCreated;
use Statamic\Events\GlobalVariablesDeleted;
use Statamic\Events\GlobalVariablesSaved;

class GlobalVariables extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.global-variables';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->variables->id(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            GlobalVariablesCreated::class => __('statamic-logger::verbs.created'),
            GlobalVariablesDeleted::class => __('statamic-logger::verbs.deleted'),
            GlobalVariablesSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
