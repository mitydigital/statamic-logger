<div>{{ __('statamic-logger::listeners.addon_settings', [
        'action' => $handler->action(),
            'name' => $data->name,
            'addon' => $data->name
            ]) }}
</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>