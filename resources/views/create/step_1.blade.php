@extends('master')

@section('header')
    <script src="{{ asset('js/app/create_poll.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('css/app/create_poll.css') }}">
@endsection

@section('main')
    <div class="row" id="form-block">
        <div class="col-md-8 col-md-offset-2">
            <form name="pollform" id="pollform" method="POST" class="form-horizontal" role="form">
                @csrf
                <div class="alert alert-info">
                    <p>
                        @lang('step_1.You are in the poll creation section.')<br/>
                        @lang('step_1.Required fields cannot be left blank.')
                    </p>
                </div>

                <div class="form-group @if ($errors->has('name')) has-error @endif">
                    <label for="yourname" class="col-sm-4 control-label">@lang('generic.Your name') *</label>

                    <div class="col-sm-8">
                        @if (!Auth::guest())
                            <input id="yourname" type="text" name="name" class="form-control" readonly value="{{ Auth::user()->name }}" />
                        @else
                            <input id="yourname" type="text" name="name" class="form-control" value="{{ old('name') ?? (session()->has('form') ? session()->get('form')->admin_name : '') }}" />
                        @endif
                    </div>
                </div>
                @if ($errors->has('name'))
                    <div class="alert alert-danger">
                        @foreach ($errors->get('name') as $message)
                        <p id="poll_title_error">
                            {{ $message }}
                        </p>
                        @endforeach
                    </div>
                @endif

                @if ($use_smtp)
                    <div class="form-group @if ($errors->has('mail')) has-error @endif">
                        <label for="email" class="col-sm-4 control-label">
                            @lang('generic.Your email address') *<br/>
                            <span class="small">@lang('generic.(in the format name@mail.com)')</span>
                        </label>

                        <div class="col-sm-8">
                            @if (!Auth::guest())
                                <input id="email" type="text" name="mail" class="form-control" readonly value="{{ Auth::user()->email }}" />
                            @else
                                <input id="email" type="text" name="mail" class="form-control" value="{{ old('mail') ?? (session()->has('form') ? session()->get('form')->admin_mail : '') }}" />
                            @endif
                        </div>
                    </div>
                    @if ($errors->has('mail'))
                        <div class="alert alert-danger">
                            @foreach ($errors->get('mail') as $message)
                            <p id="poll_title_error">
                                {{ $message }}
                            </p>
                            @endforeach
                        </div>
                    @endif
                @else
                    <input id="email" type="hidden" name="mail" value="nosmtp@laradate.xyz" />
                @endif

                <div class="form-group @if ($errors->has('title')) has-error @endif">
                    <label for="poll_title" class="col-sm-4 control-label">@lang('step_1.Poll title') *</label>

                    <div class="col-sm-8">
                        <input id="poll_title" type="text" name="title" class="form-control" value="{{ old('title') ?? (session()->has('form') ? session()->get('form')->title : '') }}"/>
                    </div>
                </div>
                @if ($errors->has('title'))
                    <div class="alert alert-danger">
                        @foreach ($errors->get('title') as $message)
                        <p id="poll_title_error">
                            {{ $message }}
                        </p>
                        @endforeach
                    </div>
                @endif

                <div class="form-group @if ($errors->has('description')) has-error @endif">
                    <label for="poll_comments" class="col-sm-4 control-label">@lang('generic.Description')</label>

                    <div class="col-sm-8">
                        <textarea id="poll_comments" name="description"
                                  class="form-control"
                                  rows="5">{{ old('description') ?? (session()->has('form') ? session()->get('form')->description : '') }}</textarea>
                    </div>
                </div>
                @if ($errors->has('description'))
                    <div class="alert alert-danger">
                        @foreach ($errors->get('description') as $message)
                        <p id="poll_title_error">
                            {{ $message }}
                        </p>
                        @endforeach
                    </div>
                @endif

                {{-- Optional parameters --}}
                <div class="col-sm-offset-3 col-sm-1 hidden-xs">
                    <p class="lead">
                        <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
                    </p>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <span class="lead visible-xs-inline">
                        <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
                    </span>
                    <a class="optional-parameters collapsed lead" role="button" data-toggle="collapse" href="#optional" aria-expanded="false" aria-controls="optional">
                        @lang('step_1.Optional parameters')
                        <i class="caret" aria-hidden="true"></i>
                        <i class="caret caret-up" aria-hidden="true"></i>
                    </a>

                </div>
                <div class="clearfix"></div>


                <div class="collapse" id="optional">

                    {{-- Value MAX --}}

                    <div class="form-group">
                        <label for="use_value_max" class="col-sm-4 control-label">
                            @lang('step_1.Value Max')<br/>
                        </label>

                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="use_value_max" @if (old('use_value_max')) checked @endif
                                           id="use_value_max">
                                    @lang('step_1.Limit the amount of voters per option')
                                </label>
                            </div>
                        </div>

                        <div id="value_max_options" @if (!old('use_value_max')) class="hidden" @endif>
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="input-group">
                                    <input id="value_max" name="value_max" type="number" min="0" class="form-control"/>
                                    <label for="value_max" class="input-group-addon">@lang('step_1.valueMax instructions')</label>
                                </div>
                            </div>
                            @if ($errors->has('value_max'))
                                <div class="alert alert-danger">
                                    @foreach ($errors->get('value_max') as $message)
                                        <p id="poll_value_max_error">
                                            {{ $message }}
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>


                {{-- Poll identifier --}}

                    <div class="form-group @if ($errors->has('customized_url')) has-error @endif">
                        <label for="poll_id" class="col-sm-4 control-label">
                            @lang('step_1.Poll id')<br/>
                        </label>

                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input id="use_customized_url" name="use_customized_url" type="checkbox" @if (old('use_customized_url')) checked @endif/>
                                    @lang('step_1.Customize the URL')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="customized_url_options" @if (!old('use_customized_url')) class="hidden" @endif>
                        <div class="form-group @if ($errors->has('customized_url')) has-error @endif">
                            <label for="customized_url" class="col-sm-4 control-label">
                                <span id="pollUrlDesc" class="small">@lang('step_1.Poll id rules')</span>
                            </label>

                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        {{ url('/') }}
                                    </span>
                                    <input id="customized_url" type="text" name="customized_url" class="form-control"
                                           value="{{ old('customized_url') }}" aria-describedBy="pollUrlDesc" maxlength="64"
                                           pattern="[A-Za-z0-9-]+"/>
                                </div>
                                <span class="help-block text-warning">@lang('step_1.Poll id warning')</span>
                            </div>
                        </div>
                        @if ($errors->has('customized_url'))
                            <div class="alert alert-danger">
                                @foreach ($errors->get('customized_url') as $message)
                                <p id="poll_customized_url_error">
                                    {{ $message }}
                                </p>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Password --}}

                    <div class="form-group">
                        <label for="poll_id" class="col-sm-4 control-label">
                            @lang('step_1.Poll password')
                        </label>

                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="use_password" @if (old('use_password')) checked @endif
                                           id="use_password">
                                    @lang('step_1.Use a password to restrict access')
                                </label>
                            </div>
                        </div>

                        <div id="password_options" @if (!old('use_password')) class="hidden" @endif>
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="input-group">
                                    <input id="poll_password" type="password" name="password" class="form-control"/>
                                    <label for="poll_password" class="input-group-addon">@lang('step_1.Password choice')</label>
                                </div>
                            </div>
                            @if ($errors->has('password'))
                                <div class="alert alert-danger">
                                    @foreach ($errors->get('password') as $message)
                                    <p id="poll_password_error">
                                        {{ $message }}
                                    </p>
                                    @endforeach
                                </div>
                            @endif
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="input-group">
                                    <input id="poll_password_repeat" type="password" name="password_repeat" class="form-control"/>
                                    <label for="poll_password_repeat" class="input-group-addon">@lang('step_1.Password confirmation')</label>
                                </div>
                            </div>
                            @if ($errors->has('password_repeat'))
                                <div class="alert alert-danger">
                                    @foreach ($errors->get('password_repeat') as $message)
                                    <p id="poll_password_repeat_error">
                                        {{ $message }}
                                    </p>
                                    @endforeach
                                </div>
                            @endif
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="results_publicly_visible"
                                               @if (old('results_publicly_visible')) checked @endif id="results_publicly_visible"/>
                                        @lang('step_1.The results are publicly visible')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="poll_id" class="col-sm-4 control-label">
                            @lang('step_1.Permissions')
                        </label>
                        <div class="col-sm-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="editable" id="editableByAll" @if (old('editable') == 1) checked @endif value="1">
                                    @lang('step_1.All voters can modify any vote')
                                </label>
                                <label>
                                    <input type="radio" name="editable" @if (old('editable') == 2) checked @endif value="2">
                                    @lang('step_1.Voters can modify their vote themselves')
                                </label>
                                <label>
                                    <input type="radio" name="editable" @if (old('editable') == 0 || !old('editable')) checked @endif value="0">
                                    @lang('step_1.Votes cannot be modified')
                                </label>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('editable'))
                        <div class="alert alert-danger">
                            @foreach ($errors->get('editable') as $message)
                                <p id="poll_password_repeat_error">
                                    {{ $message }}
                                </p>
                            @endforeach
                        </div>
                    @endif


                    @if ($use_smtp)
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="receiveNewVotes" @if (old('receiveNewVotes')) checked @endif
                                        id="receiveNewVotes">
                                        @lang('step_1.To receive an email for each new vote')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="receiveNewComments" @if (old('receiveNewComments')) checked @endif
                                        id="receiveNewComments">
                                        @lang('step_1.To receive an email for each new comment')
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="hidden" @if (old('hidden')) checked @endif
                                    id="hidden">
                                    @lang('step_1.Only the poll maker can see the poll\'s results')
                                </label>
                            </div>
                            <div id="hiddenWithBadEditionModeError" class="alert alert-danger hidden">
                                <p>
                                    @lang('error.You can\'t create a poll with hidden results with the following edition option:') "@lang('step_1.All voters can modify any vote')"
                                </p>
                            </div>
                        </div>
                        </div>
                    </div>

                    <p class="text-right">
                        <input type="hidden" name="type" value="poll_type"/>
                        <button name="{{ $type }}" value="{{ $type }}" type="submit"
                                class="btn btn-success">@lang('step_1.Go to step 2')</button>
                    </p>

                    </form>
                </div>

                <script type="text/javascript">document.pollform.title.focus();</script>

        </div>
    <noscript>
        <div class="alert alert-danger">
            @lang('error.Javascript is disabled on your browser. Its activation is required to create a poll.')
        </div>
    </noscript>
    <div id="cookie-warning" class="alert alert-danger hide">
        @lang('error.Cookies are disabled on your browser. Theirs activation is required to create a poll.')
    </div>
@endsection
