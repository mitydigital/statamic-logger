<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventHandler;
use Statamic\Events\TaxonomyCreated;
use Statamic\Events\TaxonomyDeleted;
use Statamic\Events\TaxonomySaved;

class Taxonomy extends EventHandler
{
    public function view(): string
    {
        return 'statamic-logger::listeners.taxonomy';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->taxonomy->handle(),
            'name' => $event->taxonomy->title(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            TaxonomyCreated::class => __('statamic-logger::verbs.created'),
            TaxonomyDeleted::class => __('statamic-logger::verbs.deleted'),
            TaxonomySaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
