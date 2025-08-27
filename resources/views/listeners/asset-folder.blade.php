<div>{{ __('statamic-logger::listeners.asset_folder', [
    'action' => $handler->action(),
    'id' => $data->id,
    'container_name' => $data->container->name
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>