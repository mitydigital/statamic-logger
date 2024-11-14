<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Form;
use Statamic\Events\FormSaved;

it('returns the correct form data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $form = (\Statamic\Facades\Form::make('contact'))
        ->title('Contact');
    $form->save();

    // create the event
    $event = new FormSaved($form);

    // create the listener
    $listener = new Form;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($form->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($form->title());
});

it('returns the correct view', function () {
    $listener = new Form;

    expect($listener->view())->toBe('statamic-logger::listeners.form');
});
