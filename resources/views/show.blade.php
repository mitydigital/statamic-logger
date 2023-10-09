@extends('statamic::layout')
@section('title', __('statamic-logger::utility.title'))
@section('wrapper_class', 'max-w-full')

@section('content')

    <statamic-logger-viewer
            breadcrumb-url="{{ cp_route('utilities.index') }}"
            dates="{{ $dates }}"
            title="{{ __('statamic-logger::utility.title') }}">
    </statamic-logger-viewer>

@endsection
