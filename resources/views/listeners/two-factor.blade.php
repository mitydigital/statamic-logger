<div>{{ __('statamic-logger::listeners.two_factor.'.$handler->action(), [
    'user' => $data->name,
]) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>