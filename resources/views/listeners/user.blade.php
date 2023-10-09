<div>{{ $data->name }} {{ strtolower($handler->action($event)) }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.id') }}: {{ $data->id }}</div>