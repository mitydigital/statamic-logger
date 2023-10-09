<?php

use MityDigital\StatamicLogger\Console\Commands\ListSubscribedEventsCommand;

beforeEach(function () {
    $this->command = app(ListSubscribedEventsCommand::class);
});

it('has the correct signature', function () {
    $signature = getPrivateProperty(ListSubscribedEventsCommand::class,
        'signature');

    expect($signature->getValue($this->command))->toBe('statamic-logger:list');
});
