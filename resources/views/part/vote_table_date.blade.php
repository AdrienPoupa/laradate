@if (!is_array($best_choices) || empty($best_choices))
    <?php $best_choices = [0]; ?>
@endif

<h3>
    @lang('poll_results.Votes of the poll') @if ($hidden) <i>(@lang('pollinfo.Results are hidden'))</i> @endif
    @if ($accessGranted)
        <a href="" data-toggle="modal" data-target="#hint_modal"><i class="glyphicon glyphicon-info-sign"></i></a>
    @endif
</h3>

@include('part.scroll_left_right')

<div id="tableContainer" class="tableContainer">
    <form action=" @if ($admin) {{ \App\Utils::getPollUrl($admin_poll_id, true) }} @else {{ \App\Utils::getPollUrl($poll_id) }} @endif " method="POST" id="poll_form">
        {{ csrf_field() }}
        <input type="hidden" name="control" value="{{ $slots_hash }}"/>
        <table class="results">
            <caption class="sr-only">@lang('poll_results.Votes of the poll') {{ $poll->title }}</caption>
            <thead>
            @if ($admin && !$expired)
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    <?php $headersDCount = 0; ?>
                    @foreach ($slots as $slot)
                        @foreach ($slot->moments as $id=>$moment)
                            <td headers="M{{ $headersDCount }} D{{ $headersDCount }} H{{ $headersDCount }}">
                                <a href="{{ \App\Utils::getPollUrl($admin_poll_id, true, '', 'delete_column', \App\Utils::base64url_encode($slot->day.'@'.$moment)) }}"
                                   data-remove-confirmation="@lang('adminpoll.Confirm removal of the column.')"
                                   class="btn btn-link btn-sm remove-column"
                                   title="@lang('adminpoll.Remove the column') {{ strftime(__('date.SHORT'), $slot->day) }} - {{  $moment }}">
                                    <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only">@lang('generic.Remove')</span>
                                </a>
                            </td>
                            <?php $headersDCount++; ?>
                        @endforeach
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
                <?php $count_same = 0; ?>
                <?php $previous = 0; ?>
                @foreach ($slots as $id=>$slot)
                    <?php $display = strftime(__('date.MONTH_YEAR'), $slot->day); ?>
                    @if ($previous !== 0 && $previous != $display)
                        <th colspan="{{ $count_same }}" class="bg-primary month" id="M{{ $id }}">{{ $previous }}</th>
                        <?php $count_same = 0; ?>
                    @endif

                    <?php $count_same = $count_same + count($slot->moments); ?>

                    @if ($loop->last)
                        <th colspan="{{ $count_same }}" class="bg-primary month" id="M{{ $id }}">{{ $display }}</th>
                    @endif

                    <?php $previous = $display; ?>

                    @for ($foo=0; $foo <= count($slot->moments)-1; $foo++)
                        <?php $headersM = $id; ?>
                    @endfor
                @endforeach
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                @foreach ($slots as $id=>$slot)
                    <th colspan="{{ count($slot->moments) }}" class="bg-primary day" id="D{{ $id }}">{{ strftime(__('date.DAY'), $slot->day) }}</th>
                    @for ($foo=0; $foo <= count($slot->moments)-1; $foo++)
                        <?php $headersD = $id; ?>
                    @endfor
                @endforeach
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                <?php $headersDCount = 0; ?>
                <?php $slots_raw = []; ?>
                @foreach ($slots as $slot)
                    @foreach ($slot->moments as $id=>$moment)
                        <th colspan="1" class="bg-info" id="H{{ $headersDCount }}">{{ $moment }}</th>
                        <?php $headersH = $headersDCount; ?>
                        <?php $headersDCount++; ?>
                        <?php $slots_raw[] = strftime(__('date.FULL'), $slot->day) ?>
                    @endforeach
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
                            <input type="text" id="name" name="name" value="{{ $vote->name }}" class="form-control" title="@lang('generic.Your name')" placeholder="@lang('generic.Your name')" />
                        </div>
                    </td>

                    <?php $k = 0; ?>
                    @foreach ($slots as $slot)
                      @foreach ($slot->moments as $moment)
                        <?php $choice = $vote->choices[$k] ?>


                        <td class="bg-info" headers="M{{ $headersM[$k] }} D{{  $headersD[$k] }} H{{  $headersH[$k] }}">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-{{ $k }}" name="choices[{{ $k }}]" value="2" @if ($choice=='2') checked @endif />
                                    <label class="btn btn-default btn-xs" for="y-choice-{{ $k }}" title="@lang('poll_results.Vote yes for') {{ $slots_raw[$k] }}">
                                        <i class="glyphicon glyphicon-ok"></i><span class="sr-only">@lang('generic.Yes')</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-{{ $k }}" name="choices[{{ $k }}]" value="1" @if ($choice=='1') checked @endif />
                                    <label class="btn btn-default btn-xs" for="i-choice-{{ $k }}" title="@lang('poll_results.Vote ifneedbe for') {{ $slots_raw[$k] }}">
                                        (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">@lang('generic.Ifneedbe')</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-{{ $k }}" name="choices[{{ $k }}]" value="0" @if ($choice=='0') checked @endif />
                                    <label class="btn btn-default btn-xs" for="n-choice-{{ $k }}" title="@lang('poll_results.Vote no for') {{ $slots_raw[$k] }}">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">@lang('generic.No')</span>
                                    </label>
                                </li>
                                <li class="hide">
                                    <input type="radio" id="n-choice-{{ $k }}" name="choices[{{ $k }}]" value="X" @if ($choice !='2' && $choice!='1' && $choice!='0') checked @endif />
                                </li>
                            </ul>
                        </td>

                        <?php $k++ ?>
                      @endforeach
                    @endforeach

                    <td class="btn-edit"><button type="submit" class="btn btn-success btn-xs" name="save" value="{{ $vote->id }}" title="@lang('poll_results.Save the choices') {$vote->name }}">@lang('generic.Save')</button></td>

                </tr>
                @elseif (!$hidden)
                <tr>

                    {{-- Voted line --}}

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


                <?php $k = 0; ?>
                    @foreach ($slots as $slot)
                      @foreach ($slot->moments as $moment)
                        <?php $choice = $vote->choices[$k] ?>

                        @if ($choice=='2')
                            <td class="bg-success text-success" headers="M{{ $headersM[$k] }} D{{  $headersD[$k] }} H{{ $k }}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">@lang('generic.Yes')</span></td>
                        @elseif ($choice=='1')
                            <td class="bg-warning text-warning" headers="M{{ $headersM[$k] }} D{{  $headersD[$k] }} H{{ $k }}">(<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">@lang('generic.Ifneedbe')</span></td>
                        @elseif ($choice=='0')
                            <td class="bg-danger text-danger" headers="M{{ $headersM[$k] }} D{{  $headersD[$k] }} H{{ $k }}"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">@lang('generic.No')</span></td>
                        @else
                            <td class="bg-info" headers="M{{ $headersM[$k] }} D{{  $headersD[$k] }} H{{ $k }}"><span class="sr-only">@lang('generic.Unknown')</span></td>
                        @endif

                        <?php $k++ ?>
                      @endforeach
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
                    <?php $i = 0; ?>
                    @foreach ($slots as $slot)
                        @foreach ($slot->moments as $moment)
                            <td class="bg-info" headers="M{{ $headersM[$i] }} D{{ $headersD[$i] }} H{{ $headersH[$i] }}">
                                <ul class="list-unstyled choice">
                                    @if ($poll->valueMax == NULL || isset($best_choices['y'][$i]) && $best_choices['y'][$i] < $poll->valueMax)
                                        <li class="yes">
                                            <input type="radio" id="y-choice-{{ $i }}" name="choices[{{ $i }}]" value="2" />
                                            <label class="btn btn-default btn-xs" for="y-choice-{{ $i }}" title="@lang('poll_results.Vote yes for') {{ strftime(__('date.SHORT'), $slot->day) }} - {{ $moment }}">
                                                <i class="glyphicon glyphicon-ok"></i><span class="sr-only">@lang('generic.Yes')</span>
                                            </label>
                                        </li>
                                        <li class="ifneedbe">
                                            <input type="radio" id="i-choice-{{ $i }}" name="choices[{{ $i }}]" value="1" />
                                            <label class="btn btn-default btn-xs" for="i-choice-{{ $i }}" title="@lang('poll_results.Vote ifneedbe for') {{ strftime(__('date.SHORT'), $slot->day) }}- {{ $moment }}">
                                                (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">@lang('generic.Ifneedbe')</span>
                                            </label>
                                        </li>
                                    @endif
                                    <li class="no">
                                        <input type="radio" id="n-choice-{{ $i }}" name="choices[{{ $i }}]" value="0" />
                                        <label class="btn btn-default btn-xs startunchecked" for="n-choice-{{ $i }}" title="@lang('poll_results.Vote no for') {{ strftime(__('date.SHORT'), $slot->day) }} - {{ $moment }}">
                                            <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">@lang('generic.No')</span>
                                        </label>
                                    </li>
                                    <li class="hide">
                                      <input type="radio" id="n-choice-{{ $i }}" name="choices[{{ $i }}]" value="X" checked/>
                                    </li>
                                </ul>
                            </td>
                            <?php $i++; ?>
                        @endforeach
                    @endforeach
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="@lang('poll_results.Save the choices')">@lang('generic.Save')</button></td>
                </tr>
            @endif

            @if (!$hidden)
                {{-- Line displaying best moments --}}
                <?php $count_bests = 0; ?>
                <?php $max = max($best_choices['y']); ?>

                @if ($max > 0)
                    <tr id="addition">
                        <td>@lang('poll_results.Addition')<br/>{{ count($votes) }} @if ((count($votes))==1) @lang('poll_results.polled user') @else @lang('poll_results.polled users') @endif </td>
                        @foreach ($best_choices['y'] as $i=>$best_moment)
                            @if ($max == $best_moment)
                                <?php $count_bests++; ?>
                                <td><i class="glyphicon glyphicon-star text-info"></i><span class="yes-count">{{ $best_moment }}</span> @if ($best_choices['inb'][$i]>0) <br/><span class="small text-muted">(+<span class="inb-count">{{ $best_choices['inb'][$i] }}</span>)</span> @endif </td>
                            @elseif ($best_moment > 0)
                                <td><span class="yes-count">{{ $best_moment }}</span>@if ($best_choices['inb'][$i]>0)<br/><span class="small text-muted">(+<span class="inb-count">{{ $best_choices['inb'][$i] }}</span>)</span> @endif </td>
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
                        resIfneedbe.push(inbCountText)
                    } else {
                        resIfneedbe.push(0);
                    }
                    var yesCountText = $(this).find('.yes-count').text();
                    if(yesCountText != '' && yesCountText != undefined) {
                        resYes.push(yesCountText)
                    } else {
                        resYes.push(0);
                    }
                });
                var cols = [
                @foreach ($slots as $slot)
                    @foreach ($slot->moments as $moment)
                        $('<div/>').html('{{ strftime(__('date.FULL'), $slot->day) }} - {{  $moment }}').text(),
                    @endforeach
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
    <?php $max = max($best_choices['y']) ?>
    @if ($max > 0)
        <div class="row">
        @if ($count_bests == 1)
        <div class="col-sm-12"><h3>@lang('poll_results.Best choice')</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-info">
            <p><i class="glyphicon glyphicon-star text-info"></i> @lang('poll_results.The best choice at this time is:')</p>
            @elseif ($count_bests > 1)
            <div class="col-sm-12"><h3>@lang('poll_results.Best choices')</h3></div>
            <div class="col-sm-6 col-sm-offset-3 alert alert-info">
                <p><i class="glyphicon glyphicon-star text-info"></i> @lang('poll_results.The bests choices at this time are:')</p>
                @endif


                <?php $i = 0; ?>
                <ul class="list-unstyled">
                    @foreach ($slots as $slot)
                        @foreach ($slot->moments as $moment)
                            @if ($best_choices['y'][$i] == $max)
                                <li><strong>{{ strftime(__('date.FULL'), $slot->day) }} - {{  $moment }}</strong></li>
                            @endif
                            <?php $i++; ?>
                        @endforeach
                    @endforeach
                </ul>
                <p>@lang('generic.with') <b>{{ $max }}</b> @if ($max==1) @lang('generic.vote') @else @lang('generic.votes') @endif.</p>
            </div>
        </div>
    @endif
@endif
