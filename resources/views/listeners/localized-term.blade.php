<div>{{ __('statamic-logger::listeners.localized_term', [
    'action' => $handler->action(),
    'name' => $data->name,
    'taxonomy_name' => $data->taxonomy->name,
    'site' => $data->site->name
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>