<p>@lang('findpolls.Here is the list of the polls that you manage on :s:', ['s' => config('app.name')])</p>
<ul>
    @foreach ($polls as $poll)
        <li>
            <a href="{{ \App\Utils::getPollUrl($poll->admin_id, true) }}">{{ $poll->title }}</a>
            @lang('generic.Creation date:') {{ strftime(__('date.FULL'), strtotime($poll->creation_date)) }}
        </li>
    @endforeach
</ul>
<p>@lang('findpolls.Have a good day!')</p>
<p>
    <i>
        @lang('findpolls.PS: this email has been sent because you – or someone else – asked to get back the polls created with your email address.')
        @lang('findpolls.If you weren\'t the source of this action and if you think this is an abuse of the service, please notify the administrator on :s.', ['s' => '<a href="mailto:'.config('laradate.ADMIN_MAIL').'">'.config('laradate.ADMIN_MAIL').'</a>'])
    </i>
</p>
<br/>
@lang('mail.Thanks for your trust.')
<br/>
{{ config('app.name') }}
<hr/> @lang('mail.FOOTER')