@extends('master')

@section('main')
    <div class="alert alert-warning text-center">
        <h2>{{ $error }}</h2>
        <p>@lang('generic.Back to the homepage of') <a href="{{ url('/') }}">{{ config('app.name') }}</a></p>
    </div>
@endsection
