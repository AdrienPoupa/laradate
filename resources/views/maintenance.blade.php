@extends('master')

@section('main')
    <div class="alert alert-warning text-center">
        <h2>@lang('maintenance.The application') {{ config('app.name') }} @lang('maintenance.is currently under maintenance.')</h2>
        <p>@lang('maintenance.Thank you for your understanding.')</p>
    </div>
@endsection