<hr role="presentation" id="comments" class="hidden-print"/>

{{-- Comment list --}}
@include('part.comments_list')

{{-- Add comment form --}}
@if ($poll->active && !$expired && $accessGranted)
    <form action="{{ \App\Utils::getPollUrl($poll_id, false, '', 'comment') }}" method="POST" id="comment_form">
        @csrf

        <input type="hidden" name="poll" value="{{ $poll_id }}"/>
        @if (!empty($admin_poll_id))
            <input type="hidden" name="poll_admin" value="{{ $admin_poll_id }}"/>
        @endif
        <div class="hidden-print jumbotron">
            <div class="col-md-6 col-md-offset-3">
                <fieldset id="add-comment"><legend>@lang('comments.Add a comment to the poll')</legend>
                    <div class="form-group">
                        <label for="comment_name" class="control-label">@lang('generic.Your name')</label>
                        <input type="text" name="name" id="comment_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label">@lang('comments.Your comment')</label>
                        <textarea name="comment" id="comment" class="form-control" rows="2" cols="40"></textarea>
                    </div>
                    <div class="pull-right">
                        <input type="submit" id="add_comment" name="add_comment" value="@lang('comments.Send the comment')" class="btn btn-success">
                    </div>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
@endif