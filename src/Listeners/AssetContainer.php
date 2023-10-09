<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventHandler;
use Statamic\Events\AssetContainerCreated;
use Statamic\Events\AssetContainerDeleted;
use Statamic\Events\AssetContainerSaved;

class AssetContainer extends EventHandler
{
    public function view(): string
    {
        return 'statamic-logger::listeners.asset-container';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->container->id,
            'name' => $event->container->title,
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            AssetContainerCreated::class => __('statamic-logger::verbs.created'),
            AssetContainerDeleted::class => __('statamic-logger::verbs.deleted'),
            AssetContainerSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
