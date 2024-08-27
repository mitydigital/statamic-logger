<div>{{ __('statamic-logger::listeners.submission', [
    'action' => $handler->action(),
    'form' => $data->form
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>