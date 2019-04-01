<?php $admin = (isset($admin)) ? $admin : false; ?>

@if ($admin) <form action="{{ \App\Utils::getPollUrl($admin_poll_id, true) }}" method="POST"> @csrf @endif
    <div class="jumbotron @if ($admin) bg-danger @endif">
        <div class="row"> {{-- Title | buttons--}}
            <div id="title-form" class="col-md-7">
                <h3>{{ $poll->title }} @if ($admin && !$expired) <button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the title') }}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button> @endif </h3>
                @if ($admin && !$expired)
                    <div class="hidden js-title">
                        <label class="sr-only" for="newtitle">@lang('pollinfo.Title')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newtitle" name="title" size="40" value="{{ $poll->title }}" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success" name="update_poll_info" value="title" title="@lang('pollinfo.Save the new title')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                                <button class="btn btn-link btn-cancel" title="@lang('pollinfo.Cancel the title edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                            </span>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-5 hidden-print">
                <div class="btn-group pull-right">
                    <button onclick="print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> @lang('pollinfo.Print')</button>
                    @if ($admin)
                        <a href="{{ \App\Utils::getPollUrl($poll_id, false, '', 'csv', $admin_poll_id) }}" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> @lang('pollinfo.Export to CSV')</a>
                    @else
                        @if (!$hidden)
                            <a href="{{ \App\Utils::getPollUrl($poll_id, false, '', 'csv') }}" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> @lang('pollinfo.Export to CSV')</a>
                        @endif
                    @endif
                    @if ($admin)
                        @if (!$expired)
                        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-trash"></span> <span class="sr-only">@lang('generic.Remove')</span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'remove_all_votes') }}">@lang('pollinfo.Remove all the votes')</a></li>
                            <li><a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'remove_all_comments') }}">@lang('pollinfo.Remove all the comments')</a></li>
                            <li class="divider" role="presentation"></li>
                            <li><a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'delete_poll') }}">@lang('pollinfo.Remove the poll')</a></li>
                        </ul>
                        @else
                            <a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'delete_poll') }}" class="btn btn-danger" title="@lang('pollinfo.Remove the poll')">
                                <span class="glyphicon glyphicon-trash"></span>
                                <span class="sr-only">@lang('pollinfo.Remove the poll')</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="row"> {{-- Admin name + email | Description --}}
            <div class="form-group col-md-4">
                <div id="name-form">
                    <label class="control-label">@lang('pollinfo.Initiator of the poll')</label>
                    <p class="form-control-static">{{ $poll->admin_name }} @if ($admin && !$expired)  <button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the name') }}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button> @endif </p>
                    @if ($admin && !$expired)
                    <div class="hidden js-name">
                        <label class="sr-only" for="newname">@lang('pollinfo.Initiator of the poll')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newname" name="name" size="40" value="{{ $poll->admin_name }}" />
                            <span class="input-group-btn">
                            <button type="submit" class="btn btn-success" name="update_poll_info" value="name" title="@lang('pollinfo.Save the new name')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                            <button class="btn btn-link btn-cancel" title="@lang('pollinfo.Cancel the name edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
                @if ($admin)
                <div id="email-form">
                    <p>{{ $poll->admin_mail }} @if (!$expired) <button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the email adress') }}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button>@endif</p>
                    @if (!$expired)
                        <div class="hidden js-email">
                            <label class="sr-only" for="admin_mail">@lang('pollinfo.Email')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="admin_mail" name="admin_mail" size="40" value="{{ $poll->admin_mail }}" />
                            <span class="input-group-btn">
                                <button type="submit" name="update_poll_info" value="admin_mail" class="btn btn-success" title="@lang('pollinfo.Save the email address')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                                <button class="btn btn-link btn-cancel" title="@lang('pollinfo.Cancel the email address edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                            </span>
                            </div>
                        </div>
                    @endif
                </div>
                @endif
            </div>
            @if ($admin || preg_match('/[^ \r\n]/', $poll->description))
                <div class="form-group col-md-8" id="description-form">
                    <label class="control-label">@lang('generic.Description') @if ($admin && !$expired)  <button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the description')"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button>@endif</label>
                    <pre class="form-control-static well poll-description">{{ $poll->description }}</pre>
                    @if ($admin && !$expired)
                        <div class="hidden js-desc text-right">
                            <label class="sr-only" for="newdescription">@lang('generic.Description')</label>
                            <textarea class="form-control" id="newdescription" name="description" rows="2" cols="40">{{ $poll->description }}</textarea>
                            <button type="submit" id="btn-new-desc" name="update_poll_info" value="description" class="btn btn-sm btn-success" title="@lang('pollinfo.Save the description')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                            <button class="btn btn-default btn-sm btn-cancel" title="@lang('pollinfo.Cancel the description edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        <div class="row">
        </div>

        <div class="row">
            <div class="form-group form-group @if ($admin) col-md-4 @else col-md-6 @endif">
                <label for="public-link"><a class="public-link" href="{{ \App\Utils::getPollUrl($poll_id) }}">@lang('pollinfo.Public link of the poll') <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                <input class="form-control" id="public-link" type="text" readonly="readonly" value="{{ \App\Utils::getPollUrl($poll_id) }}" onclick="select();"/>
            </div>
            @if ($admin)
                <div class="form-group col-md-4">
                    <label for="admin-link"><a class="admin-link" href="{{ \App\Utils::getPollUrl($admin_poll_id, true) }}">@lang('pollinfo.Admin link of the poll') <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                    <input class="form-control" id="admin-link" type="text" readonly="readonly" value="{{ \App\Utils::getPollUrl($admin_poll_id, true) }}" onclick="select();"/>
                </div>
                <div id="expiration-form" class="form-group col-md-4">
                    <label class="control-label">@lang('pollinfo.Expiration date')</label>
                    <p>{{ strftime(__('date.DATE'), strtotime($poll->end_date)) }} <button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the expiration date') }}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button></p>

                        <div class="hidden js-expiration">
                            <label class="sr-only" for="newexpirationdate">@lang('pollinfo.Expiration date')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newexpirationdate" name="expiration_date" size="40" value="{{ strftime(__('date.DATE'), strtotime($poll->end_date)) }}" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-success" name="update_poll_info" value="expiration_date" title="@lang('pollinfo.Save the new expiration date')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                                    <button class="btn btn-link btn-cancel" title="@lang('pollinfo.Cancel the expiration date edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                                </span>
                            </div>
                        </div>

                </div>
            @endif
        </div>
        @if ($admin)
            <div class="row">
                <div class="col-md-4">
                    <div id="password-form">

                        <p class=""><span class="glyphicon glyphicon-lock"> </span>
                            @if (!empty($poll->password_hash) && !$poll->results_publicly_visible)
                                @lang('pollinfo.Password protected')
                            @elseif (!empty($poll->password_hash) && $poll->results_publicly_visible)
                                @lang('pollinfo.Votes protected by password')
                            @else
                                @lang('pollinfo.No password')
                            @endif
                            <button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the poll rules') }}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button></p>
                        <div class="hidden js-password">
                            <button class="btn btn-link btn-cancel" title="@lang('pollinfo.Cancel the rules edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                            @if (!empty($poll->password_hash))
                                <div class="input-group">
                                    <input type="checkbox" id="removePassword" name="removePassword"/>
                                    <label for="removePassword">@lang('pollinfo.Remove password')</label>
                                    <button type="submit" name="update_poll_info" value="removePassword" class="btn btn-success hidden" title="@lang('pollinfo.Save the new rules')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Remove password.')</span></button>
                                </div>
                            @endif
                            <div id="password_information">
                                <div class="input-group">
                                    <input type="checkbox" id="resultsPubliclyVisible" name="resultsPubliclyVisible" @if ($poll->results_publicly_visible && $poll->hidden == false && (!empty($poll->password_hash))) checked="checked" @elseif ($poll->hidden == true || empty($poll->password_hash)) disabled="disabled" @endif/>
                                    <label for="resultsPubliclyVisible">@lang('pollinfo.Only votes are protected')</label>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="password" name="password"/>
                                    <span class="input-group-btn">
                                        <button type="submit" name="update_poll_info" value="password" class="btn btn-success" title="@lang('pollinfo.Save the new rules')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 ">
                    <div id="poll-hidden-form">
                        @if ($poll->hidden)
                            <?php $hidden_icon = "glyphicon-eye-close" ?>
                            <?php $hidden_text = __('pollinfo.Results are hidden') ?>
                        @else
                            <?php $hidden_icon = "glyphicon-eye-open" ?>
                            <?php $hidden_text = __('pollinfo.Results are visible') ?>
                        @endif
                        <p class=""><span class="glyphicon {{ $hidden_icon }}"> </span> {{ $hidden_text }}<button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the poll rules') }}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button></p>
                        <div class="hidden js-poll-hidden">
                            <div class="input-group">
                                <input type="checkbox" id="hidden" name="hidden" @if ($poll->hidden) checked="checked" @endif />
                                <label for="hidden">@lang('pollinfo.Results are hidden')</label>
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="hidden" class="btn btn-success" title="@lang('pollinfo.Save the new rules')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                                    <button class="btn btn-link btn-cancel" title="@lang('pollinfo.Cancel the rules edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" >
                    <div id="poll-rules-form">
                        @if ($poll->active)
                            @if ($poll->editable)
                                @if ($poll->editable == config('laradate.EDITABLE_BY_ALL'))
                                    <?php $rule_id = 2 ?>
                                    <?php $rule_txt = __('step_1.All voters can modify any vote') ?>
                                @else
                                    <?php $rule_id = 3 ?>
                                    <?php $rule_txt = __('step_1.Voters can modify their vote themselves') ?>
                                @endif
                                <?php $rule_icon = '<span class="glyphicon glyphicon-edit"></span>' ?>
                            @else
                                <?php $rule_id = 1 ?>
                                <?php $rule_icon = '<span class="glyphicon glyphicon-check"></span>' ?>
                                <?php $rule_txt = __('step_1.Votes cannot be modified') ?>
                            @endif
                        @else
                            <?php $rule_id = 0 ?>
                            <?php $rule_icon = '<span class="glyphicon glyphicon-lock"></span>' ?>
                            <?php $rule_txt = __('pollinfo.Votes and comments are locked') ?>
                        @endif
                        <p class="">{!! $rule_icon !!} {{ $rule_txt }} <button class="btn btn-link btn-sm btn-edit" title="@lang('pollinfo.Edit the poll rules') }}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">@lang('generic.Edit')</span></button></p>
                        <div class="hidden js-poll-rules">
                            <label class="sr-only" for="rules">@lang('pollinfo.Poll rules')</label>
                            <div class="input-group">
                                <select class="form-control" id="rules" name="rules">
                                    <option value="0" @if ($rule_id==0) selected="selected" @endif>@lang('pollinfo.Votes and comments are locked')</option>
                                    <option value="1" @if ($rule_id==1) selected="selected" @endif>@lang('step_1.Votes cannot be modified')</option>
                                    <option value="3" @if ($rule_id==3) selected="selected" @endif>@lang('step_1.Voters can modify their vote themselves')</option>
                                    <option value="2" @if ($rule_id==2) selected="selected" @endif>@lang('step_1.All voters can modify any vote')</option>
                                </select>
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="rules" class="btn btn-success" title="@lang('pollinfo.Save the new rules')"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">@lang('generic.Save')</span></button>
                                    <button class="btn btn-link btn-cancel" title="@lang('pollinfo.Cancel the rules edit')"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">@lang('generic.Cancel')</span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@if ($admin) </form> @endif
