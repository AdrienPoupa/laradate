<!DOCTYPE html>
<html lang="{{ \App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

@if (isset($title))
        <title>{{ $title }} - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datepicker3.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/frama.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">
    <script type="text/javascript" src="{{ asset('js/jquery-1.11.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    @if ("en" != \App::getLocale())
    <script type="text/javascript" src="{{ asset('js/locales/bootstrap-datepicker.'.\App::getLocale().'.js') }}"></script>
    @endif
    <script type="text/javascript" src="{{ asset('js/core.js') }}"></script>
    @yield('header')
</head>
<body>
    <div class="container ombre">

        <header role="banner" class="clearfix">
            @if (count(config('laradate.ALLOWED_LANGUAGES'))>1)
                <form method="get" class="hidden-print">
                    <div class="input-group input-group-sm pull-right col-xs-12 col-sm-2">
                        <select name="lang" class="form-control" title="@lang('language_selector.Select the language')" >
                            @foreach (config('laradate.ALLOWED_LANGUAGES') as $lang_key=>$lang_value)
                                <option lang="{{ substr($lang_key, 0, 2) }}" @if (substr($lang_key, 0, 2)==\App::getLocale()) selected @endif value="{{ $lang_key }}">{{ $lang_value }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default btn-sm" title="@lang('language_selector.Change the language')">OK</button>
                        </span>
                    </div>
                </form>
            @endif

            <h1 class="row col-xs-10 col-sm-8">
                <a href="{{ url('/') }}" title="@lang('generic.Home') - {{ config('app.name') }}" >
                    <img src="{{ asset(config('laradate.IMAGE_HEADER')) }}" alt="{{ config('app.name') }}" class="img-responsive"/>
                </a>
            </h1>
        </header>

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                @if (!Auth::guest())
                    <a class="navbar-brand" href="{{ url('/') }}">@lang('generic.Welcome') {{ Auth::user()->name }}</a>
                @else
                    <a class="navbar-brand" href="{{ url('/') }}">@lang('generic.Home')</a>
                @endif
                </div>
                <ul class="nav navbar-nav">
                    <li @if(strpos(request()->url(), '/create/date')) class="active" @endif><a href="{{ url('/create/date') }}">@lang('homepage.Schedule an event')</a></li>
                    <li @if(strpos(request()->url(), '/create/classic')) class="active" @endif><a href="{{ url('/create/classic') }}">@lang('homepage.Make a classic poll')</a></li>
                    <li @if(isset($title) && $title == __('homepage.Where are my polls')) class="active" @endif><a href="{{ url('/poll/find') }}">@lang('homepage.Where are my polls')</a></li>
                    @if (Auth::guest())
                        <li @if(isset($title) && $title == __('auth.login')) class="active" @endif><a href="{{ url('/login') }}">@lang('auth.login')</a></li>
                        <li @if(isset($title) && $title == __('auth.register')) class="active" @endif><a href="{{ url('/register') }}">@lang('auth.register')</a></li>
                    @else
                        @if(Auth::user()->is_admin)
                            <li @if(isset($title) && in_array($title, [__('admin.Administration'), __('admin.Polls'), __('admin.Logs'), __('admin.Purge')])) class="active" @endif><a href="{{ url('/admin') }}">@lang('admin.Administration')</a></li>
                        @endif
                        <li @if(isset($title) && $title == __('generic.Profile')) class="active" @endif><a href="{{ url('/profile') }}">@lang('generic.Profile')</a>
                        <li><a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">@lang('auth.logout')</a></li>
                    @endif
                </ul>
            </div>
        </nav>

        <main role="main">
            @if (isset($title)) <div class="h2 text-center">{{ $title }}</div> @endif
            @yield('main')
        </main>

    </div> <!-- .container -->
</body>
<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
    @csrf
</form>
</html>
