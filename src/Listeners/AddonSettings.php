<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\AddonSettingsSaved;

class AddonSettings extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.addon-settings';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->settings->addon()->id(),
            'name' => $event->settings->addon()->name(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            AddonSettingsSaved::class => __('statamic-logger::verbs.saved'),
        };
    }
}
