<div>{{ __('statamic-logger::listeners.blueprint', [
    'action' => $handler->action($event),
    'name' => $data->name,
    'namespace' => $data->namespace
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>