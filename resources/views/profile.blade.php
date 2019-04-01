@extends('master')

@section('main')
    @if (session()->has('success'))
        <div class="alert alert-dismissible alert-success" role="alert">{{ session()->get('success') }} <button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button></div>
    @endif
        <div id="form-block" class="row">
            <div class="col-md-8 col-md-offset-2">
                <form name="pollform" id="pollform" method="POST" class="form-horizontal" role="form">
                    @csrf
                    <div class="form-group">
                        <label for="yourname" class="col-sm-4 control-label">@lang('generic.Your name')</label>

                        <div class="col-sm-8">
                            <input id="yourname" type="text" name="name" class="form-control" value="{{ \Auth::user()->name }}" readonly="readonly" />
                        </div>
                    </div>
                    <div class="form-group @if ($errors->has('mail')) has-error @endif">
                        <label for="email" class="col-sm-4 control-label">
                            @lang('generic.Your email address') *<br/>
                            <span class="small">@lang('generic.(in the format name@mail.com)')</span>
                        </label>

                        <div class="col-sm-8">
                            <input id="email" type="email" name="mail" class="form-control" value="{{ \Auth::user()->email }}" />
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
                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                        <label for="password" class="col-sm-4 control-label">@lang('step_1.Poll password')</label>

                        <div class="col-sm-8">
                            <input id="password" type="password" name="password" class="form-control" />
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <div class="alert alert-danger">
                            @foreach ($errors->get('password') as $message)
                                <p id="poll_title_error">
                                    {{ $message }}
                                </p>
                            @endforeach
                        </div>
                    @endif
                    <div class="form-group @if ($errors->has('password_repeat')) has-error @endif">
                        <label for="password_repeat" class="col-sm-4 control-label">@lang('step_1.Password confirmation')</label>

                        <div class="col-sm-8">
                            <input id="password_repeat" type="password" name="password_repeat" class="form-control" />
                        </div>
                    </div>
                    @if ($errors->has('password_repeat'))
                        <div class="alert alert-danger">
                            @foreach ($errors->get('password_repeat') as $message)
                                <p id="poll_title_error">
                                    {{ $message }}
                                </p>
                            @endforeach
                        </div>
                    @endif
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <button type="submit" class="btn btn-success">@lang('generic.Update')</button>
                    </div>
                </form>
            </div>

        </div>
@endsection
