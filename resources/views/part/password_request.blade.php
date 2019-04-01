@if (!$expired && ($poll->active || $resultPubliclyVisible))
    <hr role="presentation" id="password_request" class="hidden-print"/>

    <div class="panel panel-danger password_request alert-danger">
        <div class="col-md-6 col-md-offset-3">
            <form method="POST" class="form-inline">
                @csrf
                <input type="hidden" name="poll" value="{{ $poll_id }}"/>
                <div class="form-group">
                    <label for="password" class="control-label">@lang('password.Password')</label>
                    <input type="password" name="password" id="password" class="form-control" />
                    <input type="submit" value="@lang('password.Submit access')" class="btn btn-success">
                </div>
            </form>
        </div>
        <div class="clearfix"></div>
    </div>
@endif
