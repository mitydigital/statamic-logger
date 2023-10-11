<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\AssetDeleted;
use Statamic\Events\AssetReplaced;
use Statamic\Events\AssetReuploaded;
use Statamic\Events\AssetSaved;
use Statamic\Events\AssetUploaded;

class Asset extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.asset';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->asset->id,
            'name' => $event->asset->title,
            'container' => [
                'id' => $event->asset->container->id,
                'name' => $event->asset->container->title,
            ],
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            AssetDeleted::class => __('statamic-logger::verbs.deleted'),
            AssetSaved::class => __('statamic-logger::verbs.saved'),
            AssetReplaced::class => __('statamic-logger::verbs.replaced'),
            AssetReuploaded::class => __('statamic-logger::verbs.reuploaded'),
            AssetUploaded::class => __('statamic-logger::verbs.uploaded')
        };
    }
}
