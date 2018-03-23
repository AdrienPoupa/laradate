@extends('admin.admin_page')

@section('header')
    <script src="{{ asset('js/app/admin/polls.js') }}" type="text/javascript"></script>
@endsection

@section('admin_main')
    <div class="panel panel-default" id="poll_search">
        <div class="panel-heading">@lang('generic.Search')</div>
        <div class="panel-body" style="display: none;">
            <form method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="poll" class="control-label">@lang('admin.Poll ID')</label>
                            <input type="text" name="poll" id="poll" class="form-control"
                                   value="{{ $search['poll'] }}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title" class="control-label">@lang('admin.Title')</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   value="{{ $search['title'] }}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="control-label">@lang('admin.Author')</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ $search['name'] }}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mail" class="control-label">@lang('admin.Email')</label>
                            <input type="text" name="mail" id="mail" class="form-control"
                                   value="{{ $search['mail'] }}"/>
                        </div>
                    </div>
                </div>
                <input type="submit" value="@lang('generic.Search')" class="btn btn-default"/>
            </form>
        </div>
    </div>

    <form method="POST">
        {{ csrf_field() }}
        @if ($poll_to_delete)
            <div class="alert alert-warning text-center">
                <h3>@lang('adminpoll.Confirm removal of the poll') "{{ $poll_to_delete->id }}"</h3>

                <p>
                    <button class="btn btn-default" type="submit" value="1"
                            name="annullesuppression">@lang('adminpoll.Keep the poll')</button>
                    <button type="submit" name="delete_confirm" value="{{ $poll_to_delete->id }}"
                            class="btn btn-danger">@lang('adminpoll.Delete the poll')</button>
                </p>
            </div>
        @endif
        {{ csrf_field() }}

        <div class="panel panel-default">
            <div class="panel-heading">
                @if ($count == $total) {{ $count }} @else {{ $count }} / {{  $total }}@endif @lang('admin.polls in the database at this time')
            </div>

            <div class="table-of-polls panel">
                <table class="table table-bordered table-polls">
                <tr align="center">
                    <th scope="col"></th>
                    <th scope="col">@lang('admin.Title')</th>
                    <th scope="col">@lang('admin.Author')</th>
                    <th scope="col">@lang('admin.Email')</th>
                    <th scope="col">@lang('admin.Expiration date')</th>
                    <th scope="col">@lang('admin.Votes')</th>
                    <th scope="col">@lang('admin.Poll ID')</th>
                    <th scope="col" colspan="3">@lang('admin.Actions')</th>
                </tr>
                @foreach ($polls as $poll)
                    <tr align="center">
                        <td class="cell-format">
                            @if ($poll->format === 'D')
                                <span class="glyphicon glyphicon-calendar" aria-hidden="true"
                                      title="@lang('generic.Date')"></span>
                                <span class="sr-only">@lang('generic.Date')</span>
                            @else
                                <span class="glyphicon glyphicon-list-alt" aria-hidden="true"
                                      title="@lang('generic.Classic')"></span>
                                <span class="sr-only">@lang('generic.Classic')</span>
                            @endif
                        </td>
                        <td>{{ $poll->title }}</td>
                        <td>{{ $poll->admin_name }}</td>
                        <td>{{ $poll->admin_mail }}</td>

                        @if (strtotime($poll->end_date) > time())
                            <td>{{ date('d/m/y', strtotime($poll->end_date)) }}</td>
                        @else
                            <td><span class="text-danger">{{ strtotime('d/m/Y', $poll->end_date) }}</span></td>
                        @endif
                        <td>{{ $poll->votes }}</td>
                        <td>{{ $poll->id }}</td>
                        <td><a href="{{ \App\Utils::getPollUrl($poll->id) }}" class="btn btn-link"
                               title="@lang('admin.See the poll')"><span
                                        class="glyphicon glyphicon-eye-open"></span><span
                                        class="sr-only">@lang('admin.See the poll')</span></a></td>
                        <td><a href="{{ \App\Utils::getPollUrl($poll->admin_id, true) }}" class="btn btn-link"
                               title="@lang('admin.Change the poll')"><span
                                        class="glyphicon glyphicon-pencil"></span><span
                                        class="sr-only">@lang('admin.Change the poll')</span></a></td>
                        <td>
                            <button type="submit" name="delete_poll" value="{{ $poll->id }}" class="btn btn-link"
                                    title="@lang('admin.Deleted the poll')"><span
                                        class="glyphicon glyphicon-trash text-danger"></span><span
                                        class="sr-only">@lang('admin.Deleted the poll')</span>
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>

            <div class="panel-heading">
                @lang('admin.Pages:')
                @for ($p = 1; $p <= $pages; $p++)
                    @if ($p===$page)
                        <a href="{{ url('admin/polls') }}?page={{ $p }}&{{ $search_query }}" class="btn btn-danger"
                           disabled="disabled">{{ $p }}</a>
                    @else
                        <a href="{{ url('admin/polls') }}?page={{ $p }}&{{ $search_query }}" class="btn btn-info">{{ $p }}</a>
                    @endif
                @endfor
            </div>
        </div>
    </form>
@endsection
