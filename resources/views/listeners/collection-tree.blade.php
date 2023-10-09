<div>{{ __('statamic-logger::listeners.collection_tree', [
    'action' => $handler->action($event),
    'name' => $data->name,
    'site' => $data->site
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>