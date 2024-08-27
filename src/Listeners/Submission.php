<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\SubmissionCreating;
use Statamic\Events\SubmissionDeleted;
use Statamic\Events\SubmissionSaved;
use Statamic\Events\SubmissionSaving;

class Submission extends EventListener
{
    public function view(): string
    {
        return 'statamic-logger::listeners.submission';
    }

    protected function data($event): array
    {
        return [
            'id' => $event->submission->id(),
            'form' => $event->submission->form->title(),
            'submission' => $event->submission->data()->toArray(),
        ];
    }

    protected function verb($event): string
    {
        return match ($event) {
            SubmissionCreated::class => __('statamic-logger::verbs.created'),
            SubmissionCreating::class => __('statamic-logger::verbs.creating'),
            SubmissionDeleted::class => __('statamic-logger::verbs.deleted'),
            SubmissionSaved::class => __('statamic-logger::verbs.saved'),
            SubmissionSaving::class => __('statamic-logger::verbs.saving')
        };
    }
}
