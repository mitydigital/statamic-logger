<div>{{ __('statamic-logger::listeners.asset', [
    'action' => $handler->action(),
    'name' => $data->name,
    'container_name' => $data->container->name
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>