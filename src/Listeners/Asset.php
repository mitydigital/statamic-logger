<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\AssetDeleted;
use Statamic\Events\AssetReferencesUpdated;
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
        // AssetReplaced has no "asset": it has the original and new assets instead
        $asset = $event instanceof AssetReplaced ? $event->originalAsset : $event->asset;

        return [
            'id' => $asset->id,
            'name' => $asset->title,
            'container' => [
                'id' => $asset->container->id,
                'name' => $asset->container->title,
            ],
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            AssetDeleted::class => __('statamic-logger::verbs.deleted'),
            AssetReferencesUpdated::class => __('statamic-logger::verbs.references_updated'),
            AssetSaved::class => __('statamic-logger::verbs.saved'),
            AssetReplaced::class => __('statamic-logger::verbs.replaced'),
            AssetReuploaded::class => __('statamic-logger::verbs.reuploaded'),
            AssetUploaded::class => __('statamic-logger::verbs.uploaded')
        };
    }
}
