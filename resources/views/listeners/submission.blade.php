<div>{{ __('statamic-logger::listeners.submission', [
    'action' => $handler->action(),
    'form' => $data->form
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>
@isset($data->status)
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.status') }}: {{ __('statamic-logger::listeners.submission_status.'.$data->status) }}</div>
@endisset