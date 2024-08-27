<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Listeners\Submission;
use Statamic\Events\SubmissionSaved;
use Statamic\Facades\Form;

it('returns the correct submission data structure', function () {
    // disable actual events
    Event::fake();

    // create supporting components
    $form = (Form::make('contact'))
        ->title('Contact');
    $form->save();

    $submission = $form->makeSubmission();
    $submission->data([
        'name_first' => 'First Name',
    ]);
    $submission->save();

    // create the event
    $event = new SubmissionSaved($submission);

    // create the listener
    $listener = new Submission;
    $data = getEventHandlerData($listener, $event);

    expect($data)
        ->toHaveCount(3)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($submission->id())
        // form
        ->and($data)->toHaveKey('form')
        ->and($data['form'])->toBe($form->title())
        // submission
        ->and($data)->toHaveKey('submission')
        ->and($data['submission'])->toBeArray()->toBe([
            'name_first' => 'First Name',
        ]);
});

it('returns the correct view', function () {
    $listener = new Submission;

    expect($listener->view())->toBe('statamic-logger::listeners.submission');
});
