<div>{{ $data->impersonator->name }} {{ strtolower($handler->action()) }} {{ $data->impersonated->name }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.impersonator') }}
    : {{ $data->impersonator->id }}</div>
<div class="text-xs text-gray-500">{{ __('statamic-logger::listeners.impersonated') }}
    : {{ $data->impersonated->id }}</div>