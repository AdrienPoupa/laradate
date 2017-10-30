@extends('master')

@section('main')
    <div class="h2 text-center">404</div>
    <div class="alert alert-warning text-center">
        <p>@lang('generic.Back to the homepage of') <a href="{{ url('/') }}">{{ config('app.name') }}</a></p>
    </div>
@endsection
