<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\ImpersonationEnded;
use Statamic\Events\ImpersonationStarted;

class Impersonation extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.impersonation';
    }

    protected function data($event): array
    {
        return [
            'impersonated' => $this->getUser($event->impersonated),
            'impersonator' => $this->getUser($event->impersonator),
        ];
    }

    protected function getUser($user = null): array
    {
        if (! $user) {
            return [
                'id' => null,
                'name' => __('statamic-logger::errors.unknown_user'),
            ];
        }

        $name = $user->id;
        if (method_exists($user, 'name')) {
            $name = $user->name();
        } elseif ($user->name) {
            $name = $user->name;
        }

        return [
            'id' => $user->id,
            'name' => $name,
        ];
    }

    protected function getAuthenticatedUser($event)
    {
        return $event->impersonator;
    }

    protected function verb($event): string
    {
        return match ($event) {
            ImpersonationEnded::class => __('statamic-logger::verbs.impersonation_ended'),
            ImpersonationStarted::class => __('statamic-logger::verbs.impersonation_started'),
        };
    }
}
