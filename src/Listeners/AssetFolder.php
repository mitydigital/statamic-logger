<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\AssetFolderDeleted;
use Statamic\Events\AssetFolderSaved;

class AssetFolder extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.asset-folder';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->folder->path(),
            'name' => $event->folder->title(),
            'container' => [
                'id' => $event->folder->container()->id,
                'name' => $event->folder->container()->title,
            ],
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            AssetFolderDeleted::class => __('statamic-logger::verbs.deleted'),
            AssetFolderSaved::class => __('statamic-logger::verbs.saved'),
        };
    }
}
