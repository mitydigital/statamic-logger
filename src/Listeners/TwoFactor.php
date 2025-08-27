<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\TwoFactorAuthenticationChallenged;
use Statamic\Events\TwoFactorAuthenticationDisabled;
use Statamic\Events\TwoFactorAuthenticationEnabled;
use Statamic\Events\TwoFactorAuthenticationFailed;
use Statamic\Events\TwoFactorRecoveryCodeReplaced;

class TwoFactor extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.two-factor';
    }

    protected function data($event): array
    {
        ray($event);

        if (is_null($event->user)) {
            return [
                'id' => null,
                'name' => __('statamic-logger::errors.unknown_user'),
            ];
        }

        $name = $event->user->id;
        if (method_exists($event->user, 'name')) {
            $name = $event->user->name();
        } elseif ($event->user->name) {
            $name = $event->user->name;
        }

        return [
            'id' => $event->user->id,
            'name' => $name,
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            TwoFactorAuthenticationChallenged::class => 'challenged',
            TwoFactorAuthenticationDisabled::class => 'disabled',
            TwoFactorAuthenticationEnabled::class => 'enabled',
            TwoFactorAuthenticationFailed::class => 'failed',
            TwoFactorRecoveryCodeReplaced::class => 'recovery_code_replaced',
        };
    }
}
