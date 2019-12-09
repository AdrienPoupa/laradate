<h1>@lang('editLink.Edit link for poll ":s"', ['s' => $poll->title])</h1>
<p>
    @lang('editLink.Here is the link for editing your vote:')
    <a href="{{ \App\Utils::getPollUrl($poll->id, false, $editedVoteUniqueId) }}">{{ $poll->title }}</a>
</p>
