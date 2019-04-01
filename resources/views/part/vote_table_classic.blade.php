@if (!is_array($best_choices) || empty($best_choices))
    <?php $best_choices = [0] ?>
@endif

<h3>
    @lang('poll_results.Votes of the poll') @if ($hidden) <i>(@lang('pollinfo.Results are hidden'))</i> @endif
    @if ($accessGranted)
        <a href="" data-toggle="modal" data-target="#hint_modal"><i class="glyphicon glyphicon-info-sign"></i></a><!-- TODO Add accessibility -->
    @endif
</h3>

@include('part.scroll_left_right')

<div id="tableContainer" class="tableContainer">
    <form action=" @if ($admin) {{ \App\Utils::getPollUrl($admin_poll_id, true) }} @else {{ \App\Utils::getPollUrl($poll_id) }} @endif" method="POST"  id="poll_form">
        @csrf
        <input type="hidden" name="control" value="{{ $slots_hash }}"/>
        <table class="results">
            <caption class="sr-only">@lang('poll_results.Votes of the poll') {{ $poll->title }}</caption>
            <thead>
            @if ($admin && !$expired)
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    @foreach ($slots as $id=>$slot)
                        <td headers="C{{ $id }}">
                            <a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'delete_column', \App\Utils::base64url_encode($slot->title)) }}"
                               data-remove-confirmation="@lang('adminpoll.Confirm removal of the column.')"
                               class="btn btn-link btn-sm remove-column" title="@lang('adminpoll.Remove the column') {{ $slot->title }}">
                                <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only">@lang('generic.Remove')</span>
                            </a>
                            </td>
                    @endforeach
                    <td>
                        <a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'add_column') }}"
                           class="btn btn-link btn-sm" title="@lang('adminpoll.Add a column')">
                            <i class="glyphicon glyphicon-plus text-success"></i><span class="sr-only">@lang('poll_results.Add a column')</span>
                        </a>
                    </td>
                </tr>
            @endif
            <tr>
                <th role="presentation"></th>
                @foreach ($slots as $id=>$slot)
                    <th class="bg-info" id="C{{ $id }}" title="{{ \App\Utils::markdown($slot->title, true) }}">{!! \App\Utils::markdown($slot->title, false) !!}</th>
                @endforeach
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($votes as $vote)
                {{-- Edited line --}}

                @if ($editingVoteId === $vote->uniqId && !$expired)

                <tr class="hidden-print">
                    <td class="bg-info btn-edit">
                        <div class="input-group input-group-sm" id="edit">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="hidden" name="edited_vote" value="{{ $vote->uniqId }}"/>
                            <input type="text" id="name" name="name" value="{{ $vote->name }}" class="form-control" title="@lang('generic.Your name') }}" placeholder="@lang('generic.Your name')" />
                        </div>
                    </td>

                    <?php $id = 0; ?>
                    @foreach ($slots as $slot)
                        <?php $choice=$vote->choices[$id] ?>

                        <td class="bg-info" headers="C{{ $id }}">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-{{ $id }}" name="choices[{{ $id }}]" value="2" @if ($choice=='2') checked @endif/>
                                    <label class="btn btn-default btn-xs" for="y-choice-{{ $id }}" title="@lang('poll_results.Vote yes for') {{ $slots[$id]->title }}">
                                        <i class="glyphicon glyphicon-ok"></i><span class="sr-only">@lang('generic.Yes')</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-{{ $id }}" name="choices[{{ $id }}]" value="1" @if ($choice=='1') checked @endif/>
                                    <label class="btn btn-default btn-xs" for="i-choice-{{ $id }}" title="@lang('poll_results.Vote ifneedbe for') {{ $slots[$id]->title }}">
                                        (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">@lang('generic.Ifneedbe')</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-{{ $id }}" name="choices[{{ $id }}]" value="0" @if ($choice=='0') checked @endif/>
                                    <label class="btn btn-default btn-xs" for="n-choice-{{ $id }}" title="@lang('poll_results.Vote no for') {{ $slots[$id]->title }}">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">@lang('generic.No')</span>
                                    </label>
                                </li>
                                <li class="hide">
                                    <input type="radio" id="n-choice-{{ $id }}" name="choices[{{ $id }}]" value="X" @if ($choice!='2' && $choice!='1' && $choice!='0') checked @endif/>
                                </li>
                            </ul>
                        </td>

                        <?php $id++; ?>
                    @endforeach

                    <td class="btn-edit"><button type="submit" class="btn btn-success btn-xs" name="save" value="{{ $vote->id }}" title="@lang('poll_results.Save the choices') {{ $vote->name }}">@lang('generic.Save')</button></td>
                </tr>
                @elseif (!$hidden) {{-- Voted line --}}
                <tr>

                    <th class="bg-info">{{ $vote->name }}
                        @if ($poll->active && !$expired && $accessGranted &&
                        ($poll->editable == config('laradate.EDITABLE_BY_ALL')
                        || $admin
                        || ($poll->editable == config('laradate.EDITABLE_BY_OWN') && $editedVoteUniqueId == $vote->uniqId)
                        ) && count($slots) > 4)
                            <span class="edit-username-left">
                                <a href="@if ($admin) {{ \App\Utils::getPollUrl($poll->admin_id, true, $vote->uniqId) }} @else {{ \App\Utils::getPollUrl($poll->id, false, $vote->uniqId) }} @endif " class="btn btn-default btn-sm" title="@lang('poll_results.Edit the line: :s', ['s' => $vote->name])">
                                    <i class="glyphicon glyphicon-pencil"></i><span class="sr-only">@lang('generic.Edit')</span>
                                </a>
                            </span>
                        @endif
                    </th>

                    <?php $id = 0; ?>
                    @foreach ($slots as $slot)
                        <?php $choice=$vote->choices[$id] ?>

                        @if ($choice=='2')
                            <td class="bg-success text-success" headers="C{{ $id }}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">@lang('generic.Yes')</span></td>
                        @elseif ($choice=='1')
                            <td class="bg-warning text-warning" headers="C{{ $id }}">(<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">@lang('generic.Ifneedbe')</span></td>
                        @elseif ($choice=='0')
                            <td class="bg-danger text-danger" headers="C{{ $id }}"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">@lang('generic.No')</span></td>
                        @else
                            <td class="bg-info" headers="C{{ $id }}"><span class="sr-only">@lang('generic.Unknown')</span></td>
                        @endif

                        <?php $id++; ?>
                    @endforeach

                    @if ($poll->active && !$expired && $accessGranted &&
                        (
                         $poll->editable == config('laradate.EDITABLE_BY_ALL')
                         || $admin
                         || ($poll->editable == config('laradate.EDITABLE_BY_OWN') && $editedVoteUniqueId == $vote->uniqId)
                        )
                    )

                        <td class="hidden-print">
                            <a href="@if ($admin) {{ \App\Utils::getPollUrl($poll->admin_id, true, $vote->uniqId) }} @else {{ \App\Utils::getPollUrl($poll->id, false, $vote->uniqId) }} @endif " class="btn btn-default btn-sm" title="@lang('poll_results.Edit the line: :s', ['s' => $vote->name])">
                                <i class="glyphicon glyphicon-pencil"></i><span class="sr-only">@lang('generic.Edit')</span>
                            </a>
                            @if ($admin)
                                <a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'delete_vote', $vote->id) }}"
                                   class="btn btn-default btn-sm"
                                   title="@lang('poll_results.Remove the line:') {{ $vote->name }}">
                                    <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only">@lang('generic.Remove')</span>
                                </a>
                            @endif
                        </td>
                    @else
                        <td></td>
                    @endif
                </tr>
                @endif
            @endforeach

            {{-- Line to add a new vote --}}

            @if ($poll->active && $editingVoteId === 0 && !$expired && $accessGranted)
                <tr id="vote-form" class="hidden-print">
                    <td class="bg-info btn-edit">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="name" name="name" class="form-control" title="@lang('generic.Your name')" placeholder="@lang('generic.Your name')" />
                        </div>
                    </td>
                    @foreach ($slots as $id=>$slot)
                        <td class="bg-info" headers="C{{ $id }}">
                            <ul class="list-unstyled choice">
                                @if ($poll->valueMax == null || isset($best_choices['y'][$i]) && $best_choices['y'][$i] < $poll->valueMax)
                                    <li class="yes">
                                        <input type="radio" id="y-choice-{{ $id }}" name="choices[{{ $id }}]" value="2" />
                                        <label class="btn btn-default btn-xs" for="y-choice-{{ $id }}" title="@lang('poll_results.Vote yes for') {{ $slot->title }}">
                                            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">@lang('generic.Yes')</span>
                                        </label>
                                    </li>
                                    <li class="ifneedbe">
                                        <input type="radio" id="i-choice-{{ $id }}" name="choices[{{ $id }}]" value="1" />
                                        <label class="btn btn-default btn-xs" for="i-choice-{{ $id }}" title="@lang('poll_results.Vote ifneedbe for') {{ $slot->title }}">
                                            (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">@lang('generic.Ifneedbe')</span>
                                        </label>
                                    </li>
                                @endif
                                <li class="no">
                                    <input type="radio" id="n-choice-{{ $id }}" name="choices[{{ $id }}]" value="0" />
                                    <label class="btn btn-default btn-xs startunchecked" for="n-choice-{{ $id }}" title="@lang('poll_results.Vote no for') {{ $slot->title }}">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">@lang('generic.No')</span>
                                    </label>
                                </li>
                                <li class="hide">
                                  <input type="radio" id="n-choice-{{ $id }}" name="choices[{{ $id }}]" value="X" checked/>
                                </li>
                            </ul>
                        </td>
                    @endforeach
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="@lang('poll_results.Save the choices')">@lang('generic.Save')</button></td>
                </tr>
            @endif

            @if (!$hidden)
                {{-- Line displaying best moments --}}
                <?php $count_best = 0; ?>
                <?php $max = max($best_choices['y']); ?>
                @if ($max > 0)
                    <tr id="addition">
                        <td>@lang('poll_results.Addition')<br/>{{ count($votes) }} @if ((count($votes))==1) @lang('poll_results.polled user') @else @lang('poll_results.polled users') @endif </td>
                        @foreach ($best_choices['y'] as $i=>$best_choice)
                            @if ($max == $best_choice)
                                <?php $count_best++; ?>
                                <td><i class="glyphicon glyphicon-star text-info"></i><span class="yes-count">{{ $best_choice }}</span> @if ($best_choices['inb'][$i]>0) <br/><span class="small text-muted">(+<span class="inb-count">{{ $best_choices['inb'][$i] }}</span>)</span> @endif </td>
                            @elseif ($best_choice > 0)
                                <td><span class="yes-count">{{ $best_choice }}</span>@if ($best_choices['inb'][$i]>0) <br/><span class="small text-muted">(+<span class="inb-count">{{  $best_choices['inb'][$i] }}</span>)</span> @endif </td>
                            @elseif ($best_choices['inb'][$i]>0)
                                <td><br/><span class="small text-muted">(+<span class="inb-count">{{ $best_choices['inb'][$i] }}</span>)</span></td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endif
            @endif
            </tbody>
        </table>
    </form>
</div>

@if (!$hidden && $max > 0)
    <div class="row" aria-hidden="true">
        <div class="col-xs-12">
            <p class="text-center" id="showChart">
                <button class="btn btn-lg btn-default">
                    <span class="fa fa-fw fa-bar-chart"></span> @lang('poll_results.Display the chart of the results')
                </button>
            </p>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#showChart').on('click', function() {
                $('#showChart')
                        .after("<h3>@lang('poll_results.Chart')</h3><canvas id=\"Chart\"></canvas>")
                        .remove();
                
                var resIfneedbe = [];
                var resYes = [];
            
                $('#addition').find('td').each(function () {
                    var inbCountText = $(this).find('.inb-count').text();
                    if(inbCountText != '' && inbCountText != undefined) {
                        resIfneedbe.push($(this).find('.inb-count').html())
                    } else {
                        resIfneedbe.push(0);
                    }

                    var yesCountText = $(this).find('.yes-count').text();
                    if(yesCountText != '' && yesCountText != undefined) {
                        resYes.push($(this).find('.yes-count').html())
                    } else {
                        resYes.push(0);
                    }
                });
                var cols = [
                @foreach ($slots as $id=>$slot)
                    $('<div/>').html('{{ $slot->title }}').text(),
                @endforeach
                ];

                resIfneedbe.shift();
                resYes.shift();

                var barChartData = {
                    labels : cols,
                    datasets : [
                    {
                        label: "@lang('generic.Ifneedbe')",
                        fillColor : "rgba(255,207,79,0.8)",
                        highlightFill: "rgba(255,207,79,1)",
                        barShowStroke : false,
                        data : resIfneedbe
                    },
                    {
                        label: "@lang('generic.Yes')",
                        fillColor : "rgba(103,120,53,0.8)",
                        highlightFill : "rgba(103,120,53,1)",
                        barShowStroke : false,
                        data : resYes
                    }
                    ]
                };

                var ctx = document.getElementById("Chart").getContext("2d");
                window.myBar = new Chart(ctx).StackedBar(barChartData, {
                    responsive : true
                });
                return false;
            });
        });
    </script>
    
@endif



@if (!$hidden)
    {{-- Best votes listing --}}
    <?php $max = max($best_choices['y']); ?>
    @if ($max > 0)
        <div class="row">
        @if ($count_best == 1)
        <div class="col-sm-12"><h3>@lang('poll_results.Best choice')</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-info">
            <p><i class="glyphicon glyphicon-star text-info"></i> @lang('poll_results.The best choice at this time is:')</p>
            @elseif ($count_best > 1)
            <div class="col-sm-12"><h3>@lang('poll_results.Best choices')</h3></div>
            <div class="col-sm-6 col-sm-offset-3 alert alert-info">
                <p><i class="glyphicon glyphicon-star text-info"></i> @lang('poll_results.The best choices at this time are:')</p>
                @endif


                <?php $i = 0; ?>
                <ul class="list-unstyled">
                    @foreach ($slots as $slot)
                        @if ($best_choices['y'][$i] == $max)
                            <li><strong>{{ $slot->title }}</strong></li>
                        @endif
                        <?php $i++; ?>
                    @endforeach
                </ul>
                <p>@lang('generic.with') <b>{{ $max }}</b> @if ($max==1) @lang('generic.vote') @else @lang('generic.votes') @endif.</p>
            </div>
        </div>
    @endif
@endif
