@extends('master')

@section('main')
    @if (session()->has('success'))
        <div class="alert alert-dismissible alert-success" role="alert">{{ session()->get('success') }} <button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button></div>
    @endif
    @if (session()->has('warning'))
        <div class="alert alert-dismissible alert-warning" role="alert">{{ session()->get('warning') }} <button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button></div>
    @endif
    @if (session()->has('danger'))
        <div class="alert alert-dismissible alert-danger" role="alert">{{ session()->get('danger') }} <button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button></div>
    @endif
    <form method="post">
        @csrf
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <div class="form-group">
                    <div class="input-group">
                        <label for="mail" class="input-group-addon">@lang('generic.Your email address')</label>
                        <input type="email" class="form-control" id="mail" name="mail" autofocus>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-3 text-center">
                <button type="submit" class="btn btn-success">@lang('findpolls.Send me my polls')</button>
            </div>
        </div>
    </form>
@endsection
