@extends('master')

@section('main')
<form action="{{ \App\Utils::getPollUrl($admin_poll_id, true) }}" method="POST">
    @csrf
    <div class="alert alert-danger text-center">
        <h2>@lang('adminpoll.Confirm removal of the poll')</h2>
        <p><button class="btn btn-default" type="submit" name="cancel">@lang('adminpoll.Keep the poll')</button>
            <button type="submit" name="confirm_delete_poll" class="btn btn-danger">@lang('pollinfo.Remove the poll')</button></p>
    </div>
</form>
@endsection