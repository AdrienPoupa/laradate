@extends('master')

    @section('main')
        <div class="row">
            <div class="col-xs-12">
                <a class="btn btn-default" href="{{ url('/admin') }}">@lang('admin.Back to administration')</a>
            </div>
        </div>
        @yield('admin_main')
    @endsection
