@extends('admin.admin_page')

@section('admin_main')
    @if (session()->has('info'))
        <div class="alert alert-dismissible alert-info" role="alert">{{ session()->get('info') }}<button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button></div>
    @endif
    <form method="POST">
        {{ csrf_field() }}
        <div class="text-center">
            <button type="submit" name="action" value="purge" class="btn btn-danger">@lang('admin.Purge the polls') <span class="glyphicon glyphicon-trash"></span></button>
        </div>
    </form>
@endsection
