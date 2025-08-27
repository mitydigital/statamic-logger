<div>{{ __('statamic-logger::listeners.global_variables', [
    'action' => $handler->action(),
    'id' => $data->id
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>