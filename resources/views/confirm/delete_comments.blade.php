@extends('master')

@section('main')
    <form action="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'remove_all_comments') }}" method="POST">
        @csrf
        <div class="alert alert-danger text-center">
            <h2>@lang('adminpoll.Confirm removal of all comments of the poll')</h2>
            <p><button class="btn btn-default" type="submit" name="cancel">@lang('adminpoll.Keep the comments')</button>
                <button type="submit" name="confirm_remove_all_comments" class="btn btn-danger">@lang('adminpoll.Remove the comments')</button></p>
        </div>
    </form>
@endsection