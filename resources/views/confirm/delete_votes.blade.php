@extends('master')

@section('main')
    <form action="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'remove_all_votes') }}" method="POST">
        @csrf
        <div class="alert alert-danger text-center">
            <h2>@lang('adminpoll.Confirm removal of all votes of the poll')</h2>
            <p><button class="btn btn-default" type="submit" name="cancel">@lang('adminpoll.Keep votes')</button>
                <button type="submit" name="confirm_remove_all_votes" class="btn btn-danger">@lang('adminpoll.Remove the votes')</button></p>
        </div>
    </form>
@endsection