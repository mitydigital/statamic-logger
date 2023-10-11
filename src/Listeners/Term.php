<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\TermCreated;
use Statamic\Events\TermDeleted;
use Statamic\Events\TermSaved;

class Term extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.term';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->term->id(),
            'name' => $event->term->title(),
            'taxonomy' => [
                'id' => $event->term->taxonomy()->handle(),
                'name' => $event->term->taxonomy()->title(),
            ],
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            TermCreated::class => __('statamic-logger::verbs.created'),
            TermDeleted::class => __('statamic-logger::verbs.deleted'),
            TermSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
