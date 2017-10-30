@extends('master')

@section('main')
    @if (isset($error) && $title != $error)
        <div class="h2 text-center">{{ $error }}</div>
    @endif
    <div class="alert alert-warning text-center">
        <p>@lang('generic.Back to the homepage of') <a href="{{ url('/') }}">{{ config('app.name') }}</a></p>
    </div>
@endsection
