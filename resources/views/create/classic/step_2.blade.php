@extends('master')

@section('main')
    <form name="pollform" method="POST" class="form-horizontal" role="form">
        @csrf
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="alert alert-info">
                    <p>@lang('step_2_classic.To make a generic poll you need to propose at least two choices between differents subjects.')</p>
                    <p>@lang('step_2_classic.You can add or remove additional choices with the buttons') <span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">@lang('generic.Remove')</span> <span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">@lang('generic.Add')</span></p>
                    @if (config('laradate.user_can_add_img_or_link'))
                    <p>@lang('step_2_classic.It\'s possible to propose links or images by using') <a href="https://wikipedia.org/wiki/Markdown">@lang('step_2_classic.the Markdown syntax')</a>.</p>
                    @endif
                    </div>

                @for ($i = 0; $i < $nb_choices; $i++)
                <?php $choice = isset($choices[$i]) ? $choices[$i] : new \App\Choice(); ?>
                <div class="form-group choice-field">
                    <label for="choice$i" class="col-sm-2 control-label">@lang('generic.Choice') {{ $i + 1 }}</label>
                    <div class="col-sm-10 input-group">
                        <input type="text" class="form-control" name="choices[]" size="40" value="{{ $choice->getName() }}" id="choice{{ $i }}" />
                        @if (config('laradate.user_can_add_img_or_link'))
                        <span class="input-group-addon btn-link md-a-img" title="@lang('step_2_classic.Add a link or an image') - @lang('generic.Choice') {{ $i + 1 }}" ><span class="glyphicon glyphicon-picture"></span> <span class="glyphicon glyphicon-link"></span></span>
                        @endif
                    </div>
                </div>
                @endfor

                <div class="col-md-4">
                    <div class="btn-group btn-group">
                        <button type="button" id="remove-a-choice" class="btn btn-default" title="@lang('step_2_classic.Remove a choice')"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">@lang('generic.Remove')</span></button>
                        <button type="button" id="add-a-choice" class="btn btn-default" title="@lang('step_2_classic.Add a choice')"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">@lang('generic.Add')</span></button>
                    </div>
                </div>
                <div class="col-md-8 text-right">
                    <a class="btn btn-default" href="{{  url('/create/classic') }}" title="@lang('step_2.Back to step 1')">@lang('generic.Back')</a>
                    <button name="end_other_poll" value="@lang('generic.Next')" type="submit" class="btn btn-success disabled" title="@lang('step_2.Go to step 3')">@lang('generic.Next')</button>
                </div>
            </div>
        </div>
        <div class="modal fade" id="md-a-imgModal" tabindex="-1" role="dialog" aria-labelledby="md-a-imgModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">@lang('generic.Close')</span></button>
                        <p class="modal-title" id="md-a-imgModalLabel">@lang('step_2_classic.Add a link or an image')</p>
                    </div>
                    <div class="modal-body">
                        <p class="alert alert-info">@lang('step_2_classic.These fields are optional. You can add a link, an image or both.')</p>
                        <div class="form-group">
                            <label for="md-img"><span class="glyphicon glyphicon-picture"></span> @lang('step_2_classic.URL of the image')</label>
                            <input id="md-img" type="text" placeholder="http://…" class="form-control" size="40" />
                        </div>
                        <div class="form-group">
                            <label for="md-a"><span class="glyphicon glyphicon-link"></span> @lang('generic.Link')</label>
                            <input id="md-a" type="text" placeholder="http://…" class="form-control" size="40" />
                        </div>
                        <div class="form-group">
                            <label for="md-text">@lang('step_2_classic.Alternative text')</label>
                            <input id="md-text" type="text" class="form-control" size="40" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('generic.Cancel')</button>
                        <button type="button" class="btn btn-primary">@lang('generic.Add')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript" src="{{ asset('js/app/laradatepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/app/classic_poll.js') }}"></script>
@endsection
