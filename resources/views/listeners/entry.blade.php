<div>{{ __('statamic-logger::listeners.entry', [
    'action' => $handler->action(),
    'name' => $data->name,
    'collection_name' => $data->collection->name
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>