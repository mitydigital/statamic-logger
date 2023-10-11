<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\FormCreated;
use Statamic\Events\FormDeleted;
use Statamic\Events\FormSaved;

class Form extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.form';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->form->handle(),
            'name' => $event->form->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            FormCreated::class => __('statamic-logger::verbs.created'),
            FormDeleted::class => __('statamic-logger::verbs.deleted'),
            FormSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
