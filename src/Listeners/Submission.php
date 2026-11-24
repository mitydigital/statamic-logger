<?php

namespace MityDigital\StatamicLogger\Listeners;

use MityDigital\StatamicLogger\Abstracts\EventListener;
use Statamic\Events\FormSubmitted;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\SubmissionCreating;
use Statamic\Events\SubmissionDeleted;
use Statamic\Events\SubmissionFinalized;
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
        $status = $this->getStatus($event->submission);

        return [
            'id' => $event->submission->id(),
            'form' => $event->submission->form->title(),
            'status' => $status,
            'submission' => $this->shouldLogAnswers($status) ? $event->submission->data()->toArray() : [],
        ];
    }

    /**
     * Submissions only have a status from Statamic 6.44.
     */
    protected function getStatus($submission): ?string
    {
        return method_exists($submission, 'status') ? $submission->status() : null;
    }

    protected function shouldLogAnswers(?string $status): bool
    {
        return ! in_array($status, ['partial', 'spam']);
    }

    protected function verb($event): string
    {
        return match ($event) {
            FormSubmitted::class => __('statamic-logger::verbs.submitted'),
            SubmissionCreated::class => __('statamic-logger::verbs.created'),
            SubmissionCreating::class => __('statamic-logger::verbs.creating'),
            SubmissionDeleted::class => __('statamic-logger::verbs.deleted'),
            SubmissionFinalized::class => __('statamic-logger::verbs.finalized'),
            SubmissionSaved::class => __('statamic-logger::verbs.saved'),
            SubmissionSaving::class => __('statamic-logger::verbs.saving')
        };
    }
}
