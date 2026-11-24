<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Fieldset;
use Statamic\Events\FieldsetReset;
use Statamic\Events\FieldsetSaved;

it('returns the correct fieldset data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $fieldset = (new Statamic\Fields\Fieldset)
        ->setHandle('my_fieldset')
        ->setContents([
            'title' => 'My Fieldset',
            'fields' => [],
        ])->save();

    // create the event
    $event = new FieldsetSaved($fieldset);

    // create the listener
    $listener = new Fieldset;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(2)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($fieldset->handle())
        // name
        ->and($data)->toHaveKey('name')
        ->and($data['name'])->toBe($fieldset->title());
});

it('logs a fieldset being reset', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $fieldset = (new Statamic\Fields\Fieldset)
        ->setHandle('my_fieldset')
        ->setContents(['title' => 'My Fieldset', 'fields' => []]);

    $event = new FieldsetReset($fieldset);

    expect(getEventHandlerData(new Fieldset, $event))->toBe([
        'id' => 'my_fieldset',
        'name' => 'My Fieldset',
    ])
        ->and(renderLoggedEvent(new Fieldset, $event))->toContain("Reset 'My Fieldset'");
});

it('returns the correct view', function () {
    $listener = new Fieldset;

    expect($listener->view())->toBe('statamic-logger::listeners.fieldset');
});
