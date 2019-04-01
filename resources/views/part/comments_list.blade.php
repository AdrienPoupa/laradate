<div id="comments_list">
    <form action="@if ($admin) {{ \App\Utils::getPollUrl($admin_poll_id, true) }} @else {{ \App\Utils::getPollUrl($poll_id) }} @endif" method="POST">
        @csrf
        @if (count($comments) > 0)
            <h3>@lang('comments.Comments of polled people')</h3>
            @foreach ($comments as $comment)
                <div class="comment">
                    @if ($admin && !$expired)
                        <a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'delete_comment', $comment->id) }}" title="@lang('comments.Remove the comment')"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">@lang('generic.Remove')</span></a>
                    @endif
                    <span class="comment_date">{{ strftime(__('date.DATETIME'), strtotime($comment->date)) }}</span>
                    <b>{{ $comment->name }}</b>&nbsp;
                    <span>{{ $comment->comment }}</span>
                </div>
            @endforeach
        @endif
    </form>
    <div id="comments_alerts"></div>
</div>