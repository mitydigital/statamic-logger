<?php

namespace MityDigital\StatamicLogger\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;
use MityDigital\StatamicLogger\Subscribers\StatamicLoggerEventSubscriber;

class ListSubscribedEventsCommand extends Command
{
    protected $signature = 'statamic-logger:list';

    protected $description = 'List all subscribed events';

    public function handle(): void
    {
        $subscriber = app()->make(StatamicLoggerEventSubscriber::class);

        // get the events
        $events = collect($subscriber->subscribe(new Dispatcher()))
            ->sortKeys()
            ->map(function ($listener, $event) {
                return [
                    'event' => $event,
                    'listener' => $listener,
                ];
            });

        // output as a table
        $this->table(
            ['Event', 'Listener'],
            $events
        );
    }
}
