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
    <form name="pollform" method="POST" class="form-horizontal" role="form">
        @csrf
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well summary">
                    <h4>@lang('step_3.List of your choices')</h4>
                    {!! $summary !!}
                </div>
                <div class="alert alert-info">
                    <p>@lang('step_3.Your poll will automatically be archived') {{ $default_poll_duration }} @lang('generic.days') @lang('step_3.after the last date of your poll.')
                        <br />@lang('step_3.You can set a closer archiving date for it.')</p>
                    <div class="form-group">
                        <label for="enddate" class="col-sm-5 control-label">@lang('step_3.Archiving date:')</label>
                        <div class="col-sm-6">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                                <input type="text" class="form-control" id="enddate" data-date-format="@lang('date.dd/mm/yyyy')" aria-describedby="dateformat" name="enddate" value="{{ $end_date_str }}" size="10" maxlength="10" placeholder="@lang('date.dd/mm/yyyy')" />
                            </div>
                        </div>
                        <span id="dateformat" class="sr-only">@lang('date.dd/mm/yyyy')</span>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <p>@lang('step_3.Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll.')</p>
                    @if ($use_smtp)
                        <p>@lang('step_3.Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.')</p>
                    @endif
                </div>
                <p class="text-right">
                    <button class="btn btn-default" onclick="window.history.back();" title="@lang('step_3.Back to step 2')">@lang('generic.Back')</button>
                    <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">@lang('step_3.Create the poll')</button>
                </p>
            </div>
        </div>
    </form>
@endsection