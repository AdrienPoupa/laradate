@extends('master')

@section('main')
<div class="row">
    <div class="col-md-6 col-xs-12">
        <a href="{{ url('admin/polls') }}"><h2>@lang('admin.Polls')</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="{{ url('admin/purge') }}"><h2>@lang('admin.Purge')</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="{{ url('admin/logs') }}"><h2>@lang('admin.Logs')</h2></a>
    </div>
</div>
@endsection
