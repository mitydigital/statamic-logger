<?php

namespace MityDigital\StatamicLogger\Listeners;

use Illuminate\Auth\Events\Failed as AuthFailed;
use Illuminate\Auth\Events\Login as AuthLogin;
use Illuminate\Auth\Events\Logout as AuthLogout;
use Illuminate\Auth\Events\PasswordReset as AuthPasswordReset;
use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\UserCreated;
use Statamic\Events\UserDeleted;
use Statamic\Events\UserSaved;

class User extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.user';
    }

    protected function data($event): array
    {
        if (is_null($event->user)) {
            return [
                'id' => null,
                'name' => __('statamic-logger::errors.unknown_user'),
            ];
        }

        return [
            'id' => $event->user->id,
            'name' => $event->user->name(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            AuthLogin::class => __('statamic-logger::verbs.logged_in'),
            AuthLogout::class => __('statamic-logger::verbs.logged_out'),
            AuthFailed::class => __('statamic-logger::verbs.login_failed'),
            AuthPasswordReset::class => __('statamic-logger::verbs.password_reset'),

            UserCreated::class => __('statamic-logger::verbs.created'),
            UserDeleted::class => __('statamic-logger::verbs.deleted'),
            UserSaved::class => __('statamic-logger::verbs.saved')
        };
    }
}
