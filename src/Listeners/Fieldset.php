<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\FieldsetCreated;
use Statamic\Events\FieldsetDeleted;
use Statamic\Events\FieldsetSaved;

class Fieldset extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.fieldset';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->fieldset->handle(),
            'name' => $event->fieldset->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            FieldsetCreated::class => __('statamic-logger::verbs.created'),
            FieldsetDeleted::class => __('statamic-logger::verbs.deleted'),
            FieldsetSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
