@extends('master')

@section('main')
    @if (session()->has('success'))
        <div class="alert alert-success text-center">
            <h2>{{ session()->get('success') }}}</h2>
            <p>@lang('generic.Back to the homepage of') <a href="{{ url('/') }}">{{ config('app.name') }}</a></p>
        </div>
    @endif
    @if (session()->has('danger'))
        <div class="alert alert-danger text-center">
            <h2>{{ session()->get('danger') }}}</h2>
            <p>@lang('generic.Back to the homepage of') <a href="{{ url('/') }}">{{ config('app.name') }}</a></p>
        </div>
    @endif
@endsection