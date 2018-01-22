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
                <div class="alert alert-info">
                    <p>@lang('adminpoll.As poll administrator, you can change all the lines of this poll with this button')
                        <span class="glyphicon glyphicon-pencil"></span><span
                                class="sr-only">@lang('generic.Edit')</span>,
                        @lang('adminpoll.remove a column or a line with') <span
                                class="glyphicon glyphicon-remove text-danger"></span><span
                                class="sr-only">@lang('generic.Remove')</span>
                        @lang('adminpoll.and add a new column with') <span
                                class="glyphicon glyphicon-plus text-success"></span><span
                                class="sr-only">@lang('adminpoll.Add a column')</span>.</p>

                    <p>@lang('adminpoll.Finally, you can change the informations of this poll like the title, the comments or your email address.')</p>

                    <p aria-hidden="true"><strong>@lang('generic.Legend:')</strong> <span
                                class="glyphicon glyphicon-ok"></span> = @lang('generic.Yes'), <b>(<span
                                    class="glyphicon glyphicon-ok"></span>)</b> = @lang('generic.Ifneedbe'), <span
                                class="glyphicon glyphicon-ban-circle"></span> = @lang('generic.No')</p>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->
