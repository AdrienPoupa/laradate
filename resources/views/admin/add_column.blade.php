@extends('master')

@section('header')
    <script type="text/javascript">
        window.date_formats = {
            DATE: '@lang('date.DATE')',
            DATEPICKER: '@lang('date.datepicker')'
        };
    </script>
    <script type="text/javascript" src="{{ asset('js/app/laradatepicker.js') }}"></script>
@endsection

@section('main')

    <form action="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'add_column') }}" method="POST">
        @csrf
        <div class="alert alert-info text-center">
            <h2>@lang('adminpoll.Column\'s adding')</h2>

            {{-- Messages --}}
            @include('part.messages')

            @if ($format === 'D')
                <div class="form-group">
                    <label for="newdate" class="col-md-4">@lang('generic.Day')</label>
                    <div class="col-md-8">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            <input type="text" id="newdate" data-date-format="@lang('date.dd/mm/yyyy')" aria-describedby="dateformat" name="newdate" class="form-control" placeholder="@lang('date.dd/mm/yyyy')" />
                        </div>
                        <span id="dateformat" class="sr-only">(@lang('date.dd/mm/yyyy'))</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="newmoment" class="col-md-4">@lang('generic.Time')</label>
                    <div class="col-md-8">
                        <input type="text" id="newmoment" name="newmoment" class="form-control" />
                    </div>
                </div>
            @else
                <div class="form-group">
                    <label for="choice" class="col-md-4">@lang('generic.Choice')</label>
                    <div class="col-md-8">
                        <input type="text" id="choice" name="choice" class="form-control" />
                    </div>
                </div>
            @endif
            <div class="form-group">
                <button class="btn btn-default" type="submit" name="back">@lang('adminpoll.Back to the poll')</button>
                <button type="submit" name="confirm_add_column" class="btn btn-success">@lang('adminpoll.Add a column')</button>
            </div>
        </div>
    </form>
@endsection