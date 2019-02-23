@extends('master')

@section('main')
    <div class="row">
        <div class="col-xs-12 col-md-6 text-center">
            <p class="home-choice">
                <a href="{{ url('/create/date') }}" class="opacity" role="button">
                    <img class="img-responsive center-block" src="{{ asset('images/date.png') }}" alt=""/>
                    <br/>
                    <span class="btn btn-primary btn-lg">
                        <span class="glyphicon glyphicon-calendar"></span>
                        @lang('homepage.Schedule an event')
                    </span>
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-md-6 text-center">
            <p class="home-choice">
                <a href="{{ url('/create/classic') }}" class="opacity" role="button">
                    <img alt="" class="img-responsive center-block" src="{{ asset('images/classic.png') }}"/>
                    <br/>
                    <span class="btn btn-info btn-lg">
                        <span class="glyphicon glyphicon-stats"></span>
                        @lang('homepage.Make a classic poll')
                    </span>
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-md-6 col-md-offset-3 text-center">
            <p class="home-choice">
                <a href="{{ url('/poll/find') }}" class="opacity" role="button">
                    <span class="btn btn-warning btn-lg">
                        <span class="glyphicon glyphicon-search"></span>
                        @lang('homepage.Where are my polls')
                    </span>
                </a>
            </p>
        </div>
    </div>
    <hr role="presentation"/>
    <div class="row">

        @if ($show_what_is_that)
            <div class="col-md-{{ $col_size }}">
                <h3>@lang('1st_section.What is that?')</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-question-sign" style="font-size:50px"></span>
                </p>

                <p>@lang('1st_section.laradate is an online service for planning an appointment or make a decision quickly and easily. No registration is required.')</p>

                <p>@lang('1st_section.Here is how it works:')</p>
                <ol>
                    <li>@lang('1st_section.Make a poll')</li>
                    <li>@lang('1st_section.Define dates or subjects to choose')</li>
                    <li>@lang('1st_section.Send the poll link to your friends or colleagues')</li>
                    <li>@lang('1st_section.Discuss and make a decision')</li>
                </ol>

                @if ($demo_poll != null)
                <p>
                    @lang('1st_section.Do you want to')
                    <a href="{{ \App\Utils::getPollUrl('aqg259dth55iuhwm') }}">@lang('1st_section.view an example?')</a>
                </p>
                @endif
            </div>
        @endif
        @if ($show_the_software)
            <div class="col-md-{{ $col_size }}">
                <h3>@lang('2nd_section.The software')</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-cloud" style="font-size:50px"></span>
                </p>

                <p>@lang('2nd_section.laradate was initially based on ')
                    <a href="https://sourcesup.cru.fr/projects/studs/">Studs</a>
                    @lang('2nd_section.a software developed by the University of Strasbourg. Today, it is devevoped by the association Framasoft.')
                </p>

                <p>@lang('2nd_section.This software needs javascript and cookies enabled. It is compatible with the following web browsers:')</p>
                <ul>
                    <li>Microsoft Internet Explorer 9+</li>
                    <li>Google Chrome 19+</li>
                    <li>Firefox 12+</li>
                    <li>Safari 5+</li>
                    <li>Opera 11+</li>
                </ul>
                <p>
                    @lang('2nd_section.It is governed by the')
                    <a href="http://www.cecill.info">@lang('2nd_section.CeCILL-B license')</a>.
                </p>
            </div>
        @endif
        @if ($show_cultivate_your_garden)
            <div class="col-md-{{ $col_size }}">
                <h3>@lang('3rd_section.Cultivate your garden')</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-tree-deciduous" style="font-size:50px"></span>
                </p>

                <p>
                    @lang('3rd_section.To participate in the software development, suggest improvements or simply download it, please visit ')
                    <a href="https://github.com/AdrienPoupa/laradate">@lang('3rd_section.the development site')</a>.
                </p>
                <br/>
            </div>
        @endif
    </div>
@endsection
