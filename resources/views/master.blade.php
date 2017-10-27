<!DOCTYPE html>
<html lang="{{ \App::getLocale() }}">
<head>
    <meta charset="utf-8">

    @if (!empty($title))
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
                <form method="get" action="" class="hidden-print">
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

            <h1 class="row col-xs-12 col-sm-10">
                <a href="{{ url('/') }}" title="@lang('generic.Home') - {{ config('app.name') }}" >
                    <img src="{{ asset(config('laradate.IMAGE_HEADER')) }}" alt="{{ config('app.name') }}" class="img-responsive"/>
                </a>
            </h1>
            @if (!empty($title)) <h2 class="lead col-xs-12"><i>{{ $title }}</i></h2> @endif
            <div class="trait col-xs-12" role="presentation"></div>
        </header>

        <main role="main">
            @yield('main')
        </main>

    </div> <!-- .container -->
</body>
</html>
