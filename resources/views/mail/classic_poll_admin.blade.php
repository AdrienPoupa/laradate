@lang('mail.This message should NOT be sent to the polled people. It is private for the poll\'s creator.\n\nYou can now modify it at the link above')
<br/><br/>
{!! sprintf('<a href="%1$s">%1$s</a>', $url) !!}
<br/><br/>
@lang('mail.Thanks for your trust.')
<br/>
{{ config('app.name') }}
<hr/> @lang('mail.FOOTER')