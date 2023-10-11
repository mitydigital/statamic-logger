<div>{{ __('statamic-logger::listeners.term', [
    'action' => $handler->action(),
    'name' => $data->name,
    'taxonomy_name' => $data->taxonomy->name
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>