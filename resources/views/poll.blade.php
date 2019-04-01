@extends('master')

@section('header')
    <script src="{{ asset('js/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/Chart.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/Chart.StackedBar.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/app/common.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
@endsection

@section('main')

    {{-- Messages --}}
    @include('part.messages')

    @if ($editableVoteHtml)
        {!! $editableVoteHtml !!}
    @endif

    @if (!$accessGranted && !$resultPubliclyVisible)

        @include('part.password_request')

    @else

        {{-- Global information about the current poll --}}
        @include('part.poll_info')

        {{-- Information about voting --}}
        @if ($expired)
            <div class="alert alert-danger">
                <p>@lang('poll.The poll is expired, it will be deleted soon.')</p>
                <p>@lang('poll.Deletion date:') {{ strftime(__('date.SHORT'), $deletion_date) }}</p>
            </div>
        @else
            @if ($admin)
                @include('part.poll_hint_admin')
            @else
                @include('part.poll_hint')
            @endif
        @endif

        @if (!$accessGranted && $resultPubliclyVisible)
            @include('part.password_request')
        @endif

        {{-- Vote table --}}
        @if ($poll->format === 'D')
            @include('part.vote_table_date')
        @else
            @include('part.vote_table_classic')
        @endif

        {{-- Comments --}}
        @include('part.comments')

    @endif

@endsection
