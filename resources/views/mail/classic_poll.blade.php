@lang('mail.This is the message you have to send to the people you want to poll.\nNow, you have to send this message to everyone you want to poll.')
<br/><br/>
{{ $admin_name }} @lang('mail.has just created a poll called') {{ $title }}
<br/><br/>
{!! sprintf(__('mail.Thanks for filling the poll at the link above') . '<a href="%1$s">%1$s</a>', $url) !!}
<br/><br/>
@lang('mail.Thanks for your trust.')
<br/>
{{ config('app.name') }}
<hr/> @lang('mail.FOOTER')