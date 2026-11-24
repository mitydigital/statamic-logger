<?php

use Illuminate\Support\Facades\Event;
use MityDigital\StatamicLogger\Http\Resources\LogResource;
use MityDigital\StatamicLogger\Listeners\Submission;
use Statamic\Events\FormSubmitted;
use Statamic\Events\SubmissionFinalized;
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
        ->toHaveCount(4)
        // id
        ->toHaveKey('id')
        ->and($data['id'])->toBe($submission->id())
        // form
        ->and($data)->toHaveKey('form')
        ->and($data['form'])->toBe($form->title())
        // status
        ->and($data)->toHaveKey('status')
        ->and($data['status'])->toBe('finalized')
        // submission
        ->and($data)->toHaveKey('submission')
        ->and($data['submission'])->toBeArray()->toBe([
            'name_first' => 'First Name',
        ]);
});

it('does not log the answers of incomplete or spam submissions', function (string $method, string $status) {
    // disable actual events
    Event::fake();

    // create supporting components
    $form = (Form::make('contact'))
        ->title('Contact');
    $form->save();

    $submission = $form->makeSubmission()
        ->data(['name_first' => 'First Name'])
        ->{$method}();

    expect(getEventHandlerData(new Submission, new SubmissionSaved($submission)))->toBe([
        'id' => $submission->id(),
        'form' => 'Contact',
        'status' => $status,
        'submission' => [],
    ]);
})->with([
    'partial' => ['asPartial', 'partial'],
    'spam' => ['markAsSpam', 'spam'],
]);

it('logs the answers when submissions do not support a status', function () {
    // submissions only have a status from Statamic 6.44
    $submission = new class
    {
        public object $form;

        public function __construct()
        {
            $this->form = new class
            {
                public function title(): string
                {
                    return 'Contact';
                }
            };
        }

        public function id(): string
        {
            return '1234';
        }

        public function data()
        {
            return collect(['name_first' => 'First Name']);
        }
    };

    $event = new SubmissionSaved($submission);

    expect(getEventHandlerData(new Submission, $event))->toBe([
        'id' => '1234',
        'form' => 'Contact',
        'status' => null,
        'submission' => ['name_first' => 'First Name'],
    ])->and(renderLoggedEvent(new Submission, $event))
        ->toContain("Saved submission for form 'Contact'")
        ->not()->toContain('Status');
});

it('renders entries that were logged without a status', function () {
    $line = '[2026-01-01 09:00:00] production.INFO: '.json_encode([
        'request' => '1234',
        'event' => SubmissionSaved::class,
        'handler' => Submission::class,
        'user' => null,
        'data' => ['id' => '1234', 'form' => 'Contact', 'submission' => []],
    ]);

    expect(html_entity_decode((new LogResource($line))->toArray(request())['detail'], ENT_QUOTES))
        ->toContain("Saved submission for form 'Contact'")
        ->not()->toContain('Status');
});

it('renders the action and status', function (string $event, string $method, string $action, string $status) {
    // disable actual events
    Event::fake();

    // create supporting components
    $form = (Form::make('contact'))
        ->title('Contact');
    $form->save();

    $submission = $form->makeSubmission();
    $submission->{$method}();

    expect(renderLoggedEvent(new Submission, new $event($submission)))
        ->toContain($action." submission for form 'Contact'")
        ->toContain('Status: '.$status);
})->with([
    'finalized' => [SubmissionFinalized::class, 'data', 'Finalized', 'Finalized'],
    'form submitted' => [FormSubmitted::class, 'asPartial', 'Submitted', 'Partial'],
    'marked as spam' => [SubmissionSaved::class, 'markAsSpam', 'Saved', 'Spam'],
]);

it('returns the correct view', function () {
    $listener = new Submission;

    expect($listener->view())->toBe('statamic-logger::listeners.submission');
});
