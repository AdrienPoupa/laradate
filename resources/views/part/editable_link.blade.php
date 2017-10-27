{{-- Messages --}}
<div id="message-container">
    <div class="alert alert-dismissible alert-success hidden-print" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="@lang('generic.Close')"><span aria-hidden="true">&times;</span></button>
        {{ $message }}
            <div class="input-group input-group-sm">
                    <span class="input-group-btn">
                        <a title="{{ $linkTitle }}" class="btn btn-default btn-sm" href="{{ $link }}">
                            <i class="glyphicon glyphicon-pencil"></i> <span class="sr-only">{{ $linkTitle }}</span>
                        </a>
                    </span>
                <input type="text" aria-hidden="true" value="{{ $link }}" class="form-control" readonly="readonly" >
            </div>
            @if ($includeTemplate)
                @include('part.form_remember_edit_link')
            @endif
    </div>
</div>
<div id="genericUnclosableSuccessTemplate" class="hidden alert alert-success hidden-print" role="alert"><span class="contents"></span></div>