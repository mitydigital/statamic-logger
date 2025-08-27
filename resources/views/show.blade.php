@php
    use MityDigital\StatamicLogger\Facades\StatamicLogger;use function Statamic\trans as __;
@endphp

@extends('statamic::layout')
@section('title', __('statamic-logger::utility.title'))

@section('content')

    <ui-header>
        <template #title>
            <div class="size-5 text-gray-500">
                {!! StatamicLogger::getIcon()  !!}
            </div>
            {{ __('statamic-logger::utility.title') }}
        </template>
    </ui-header>

    <logger-viewer
            breadcrumb-url="{{ cp_route('utilities.index') }}"
            dates="{{ $dates }}">
    </logger-viewer>

@endsection
