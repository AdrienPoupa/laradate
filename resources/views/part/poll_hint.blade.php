<div id="hint_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('generic.Caption')</h4>
            </div>
            <div class="modal-body">
                @if ($poll->active)
                    <div class="alert alert-info">
                        <p>@lang('poll.If you want to vote in this poll, you have to give your name, choose the values that fit best for you and validate with the plus button at the end of the line.')</p>

                        <p aria-hidden="true"><b>@lang('generic.Legend:')</b> <span
                                    class="glyphicon glyphicon-ok"></span>
                            = @lang('generic.Yes'), <b>(<span class="glyphicon glyphicon-ok"></span>)</b>
                            = @lang('generic.Ifneedbe'), <span class="glyphicon glyphicon-ban-circle"></span>
                            = @lang('generic.No')</p>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <p>@lang('poll.POLL_LOCKED_WARNING')</p>

                        <p aria-hidden="true"><b>@lang('generic.Legend:')</b> <span
                                    class="glyphicon glyphicon-ok"></span>
                            = @lang('generic.Yes'), <b>(<span class="glyphicon glyphicon-ok"></span>)</b>
                            = @lang('generic.Ifneedbe'), <span class="glyphicon glyphicon-ban-circle"></span>
                            = @lang('generic.No')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>