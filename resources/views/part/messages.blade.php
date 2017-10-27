{{-- Messages --}}
<div id="message-container">
    @if (session()->has('success'))
        <div class="alert alert-dismissible alert-success hidden-print" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button>
            {{ session()->get('success') }}
        </div>
    @endif
    @if (session()->has('danger'))
        <div class="alert alert-dismissible alert-danger hidden-print" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button>
            {{ session()->get('danger') }}
        </div>
    @endif
</div>
<div id="nameErrorMessage" class="hidden alert alert-dismissible alert-danger hidden-print" role="alert">@lang('error.The name is invalid.')<button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button></div>
<div id="genericErrorTemplate" class="hidden alert alert-dismissible alert-danger hidden-print" role="alert"><span class="contents"></span><button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button></div>
<div id="genericUnclosableSuccessTemplate" class="hidden alert alert-success hidden-print" role="alert"><span class="contents"></span></div>