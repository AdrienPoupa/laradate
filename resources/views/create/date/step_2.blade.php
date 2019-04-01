@extends('master')

@section('header')
    <script type="text/javascript">
        window.date_formats = {
            DATE: '@lang('date.DATE')',
            DATEPICKER: '@lang('date.datepicker')'
        };
    </script>
    <script type="text/javascript" src="{{ asset('js/app/laradatepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/app/date_poll.js') }}"></script>
@endsection

@section('main')
    <form name="pollform" method="POST" class="form-horizontal" role="form">
        @csrf
        <div class="row" id="selected-days">
            <div class="col-md-10 col-md-offset-1">
                <h3>@lang('step_2_date.Choose the dates of your poll')</h3>

                @if ($error != null)
                <div class="alert alert-danger">
                    <p>{{ $error }}</p>
                </div>
                @endif

                <div class="alert alert-info">
                    <p>@lang('step_2_date.To schedule an event you need to propose at least two choices (two hours for one day or two days).')</p>

                    <p>@lang('step_2_date.You can add or remove additionnal days and hours with the buttons')
                        <span class="glyphicon glyphicon-minus text-info"></span>
                        <span class="sr-only">@lang('generic.Remove')</span>
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="sr-only">@lang('generic.Add')</span>
                    </p>

                    <p>@lang('step_2_date.For each selected day, you can choose, or not, meeting hours (e.g.: "8h", "8:30", "8h-10h", "evening", etc.)')</p>
                </div>

                <div id="days_container">
                    @foreach ($choices as $i=>$choice)
                        @if ($choice->getName())
                            <?php $day_value = strftime(__('date.DATE'), $choice->getName()) ?>
                        @else
                            <?php $day_value = '' ?>
                        @endif
                        <fieldset>
                            <div class="form-group">
                                <legend>
                                    <label class="sr-only" for="day{{ $i }}">@lang('generic.Day') {{ $i+1 }}</label>

                                    <div class="col-xs-10 col-sm-11">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                                            <input type="text" class="form-control" id="day{{ $i }}" title="@lang('generic.Day') {{ $i+1 }}"
                                                   data-date-format="@lang('date.dd/mm/yyyy')" aria-describedby="dateformat{{ $i }}" name="days[]" value="{{ $day_value }}"
                                                   size="10" maxlength="10" placeholder="@lang('date.dd/mm/yyyy')" autocomplete="nope"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 col-sm-1">
                                        <button type="button" title="@lang('step_2_date.Remove this day')" class="remove-day btn btn-sm btn-link">
                                            <span class="glyphicon glyphicon-remove text-danger"></span>
                                            <span class="sr-only">@lang('step_2_date.Remove this day')</span>
                                        </button>
                                    </div>

                                    <span id="dateformat{{ $i }}" class="sr-only">(@lang('date.dd/mm/yyyy'))</span>
                                </legend>

                                @foreach ($choice->getSlots() as $j=>$slot)
                                    <div class="col-sm-2">
                                        <label for="d{{ $i }}-h{{ $j }}" class="sr-only control-label">@lang('generic.Time') {{ $j+1 }}</label>
                                        <input type="text" class="form-control hours" title="{{ $day_value }} - @lang('generic.Time') {{ $j+1 }}"
                                               placeholder="@lang('generic.Time') {{ $j+1 }}" id="d{{ $i }}-h{{ $j }}" name="schedule{{ $i }}[]" value="{{ $slot }}"/>
                                    </div>
                                @endforeach

                                <div class="col-sm-2">
                                    <div class="btn-group btn-group-xs" style="margin-top: 5px;">
                                        <button type="button" title="@lang('step_2_date.Remove an hour')" class="remove-an-hour btn btn-default">
                                            <span class="glyphicon glyphicon-minus text-info"></span>
                                            <span class="sr-only">@lang('step_2_date.Remove an hour')</span>
                                        </button>
                                        <button type="button" title="@lang('step_2_date.Add an hour')" class="add-an-hour btn btn-default">
                                            <span class="glyphicon glyphicon-plus text-success"></span>
                                            <span class="sr-only">@lang('step_2_date.Add an hour')</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    @endforeach
                </div>


                <div class="col-md-4">
                    <button type="button" id="copyhours" class="btn btn-default disabled" title="@lang('step_2_date.Copy hours of the first day')"><span
                                class="glyphicon glyphicon-sort-by-attributes-alt text-info"></span><span
                                class="sr-only">@lang('step_2_date.Copy hours of the first day')</span></button>
                    <div class="btn-group btn-group">
                        <button type="button" id="remove-a-day" class="btn btn-default disabled" title="@lang('step_2_date.Remove a day')"><span
                                    class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">@lang('step_2_date.Remove a day')</span></button>
                        <button type="button" id="add-a-day" class="btn btn-default" title="@lang('step_2_date.Add a day')"><span
                                    class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">@lang('step_2_date.Add a day')</span></button>
                    </div>
                    <a href="" data-toggle="modal" data-target="#add_days" class="btn btn-default" title="@lang('date.Add range dates')">
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="sr-only">@lang('date.Add range dates')</span>
                    </a>
                </div>
                <div class="col-md-8 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-remove text-danger"></span>
                            @lang('generic.Remove') <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a id="resetdays" href="javascript:void(0)">@lang('step_2_date.Remove all days')</a></li>
                            <li><a id="resethours" href="javascript:void(0)">@lang('step_2_date.Remove all hours')</a></li>
                        </ul>
                    </div>
                    <a class="btn btn-default" href="{{ url('/create/date') }}"
                       title="@lang('step_2.Back to step 1')">@lang('generic.Back')</a>
                    <button name="hourschoice" value="@lang('generic.Next')" type="submit" class="btn btn-success disabled"
                            title="@lang('step_2.Go to step 3')">@lang('generic.Next')</button>
                </div>
            </div>
        </div>
    </form>

    <div id="add_days" class="modal fade">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('date.Add range dates')</h4>
                </div>
                <div class="modal-body row">
                    <div class="col-xs-12">
                        <div class="alert alert-info">
                            @lang('date.Max dates count')
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_start">@lang('date.Start date')</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="range_start"
                                   data-date-format="@lang('date.dd/mm/yyyy')" size="10" maxlength="10"
                                   placeholder="@lang('date.dd/mm/yyyy')"/>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_end">@lang('date.End date')</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="range_end"
                                   data-date-format="@lang('date.dd/mm/yyyy')" size="10" maxlength="10"
                                   placeholder="@lang('date.dd/mm/yyyy')"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default">@lang('generic.Cancel')</button>
                    <button id="interval_add" class="btn btn-success">@lang('generic.Add')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
