<div>{{ __('statamic-logger::listeners.nav_tree', [
    'action' => $handler->action(),
    'id' => $data->id,
    'site' => $data->site
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>