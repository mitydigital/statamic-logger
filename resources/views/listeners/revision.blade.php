<div>{{ __('statamic-logger::listeners.revision', [
    'action' => $handler->action(),
    'id' => $data->id,
    'entry_id' => $data->entry->id,
    'entry_name' => $data->entry->name,
    'collection_name' => $data->collection->name,
    'site' => $data->site->name
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>