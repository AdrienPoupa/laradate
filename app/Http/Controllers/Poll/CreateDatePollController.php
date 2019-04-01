<?php

namespace App\Http\Controllers\Poll;

use App\Choice;
use App\Http\Controllers\Controller;
use App\Mail\PollAdminCreated;
use App\Mail\PollCreated;
use App\Poll;
use App\Utils;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CreateDatePollController extends Controller
{
    public function index(Request $request)
    {
        $form = session()->get('form');

        // Min/Max archive date
        $minExpiryDate = Poll::minExpiryDate();
        $maxExpiryDate = Poll::maxExpiryDate();

        // The poll format is DATE
        if ($form->format !== 'D') {
            $form->format = 'D';
            $form->clearChoices();
        }

        if (!isset($form->title) || !isset($form->admin_name) || (config('laradate.use_smtp') && !isset($form->admin_mail))) {
            $step = 1;
        } else if ($request->has('confirmation')) {
            $step = 4;
        } else if (!$request->has('hourschoice') || isset($form->totalchoixjour)) {
            $step = 2;
        } else {
            $step = 3;
        }

        switch ($step) {
            case 1:
                // Step 1/4 : error if $_SESSION from poll info is not valid
                return response()->view('errors.error', [
                    'title' => __('error.Error!'),
                    'error' => __('error.You haven\'t filled the first section of the poll creation.')
                ], 403);


            case 2:
                // Step 2/4 : Select dates of the poll

                // Prefill form->choices
                foreach ($form->getChoices() as $c) {
                    $count = 3 - count($c->getSlots());
                    for ($i = 0; $i < $count; $i++) {
                        $c->addSlot('');
                    }
                }

                $count = 3 - count($form->getChoices());
                for ($i = 0; $i < $count; $i++) {
                    $c = new Choice('');
                    $c->addSlot('');
                    $c->addSlot('');
                    $c->addSlot('');
                    $form->addChoice($c);
                }

                // Display step 2
                return view('create.date.step_2', [
                    'title' => __('step_2_date.Poll dates (2 on 3)'),
                    'choices' => $form->getChoices(),
                    'error' => null,
                ]);


            case 3:
                // Step 3/4 : Confirm poll creation

                // Handle Step2 submission
                if (!empty($request->input('days'))) {
                    // Remove empty dates
                    $days = array_filter($request->input('days'), function ($d) {
                        return !empty($d);
                    });

                    // Check if there are at most MAX_SLOTS_PER_POLL slots
                    if (count($days) > config('laradate.MAX_SLOTS_PER_POLL')) {
                        // Display step 2
                        return view('create.date.step_2', [
                            'title' => __('step_2_date.Poll dates (2 on 3)'),
                            'choices' => $form->getChoices(),
                            'error' =>  __('error.You can\'t select more than :d dates', ['d' => config('laradate.MAX_SLOTS_PER_POLL')]),
                        ]);
                    }

                    // Clear previous choices
                    $form->clearChoices();

                    // Reorder moments to deal with suppressed dates
                    $moments = [];
                    $i = 0;
                    while (count($moments) < count($days)) {
                        if ($request->has('schedule' . $i)) {
                            $moments[] = $request->input('schedule' . $i);
                        }
                        $i++;
                    }


                    for ($i = 0; $i < count($days); $i++) {
                        $day = $request->input('days')[$i];

                        if (!empty($day)) {
                            // Add choice to Form data
                            $date = DateTime::createFromFormat(__('date.datetime_parseformat'), $request->input('days')[$i])->setTime(0, 0, 0);
                            $time = $date->getTimestamp();
                            $choice = new Choice($time);
                            $form->addChoice($choice);

                            $schedules = Utils::filterArray($moments[$i], FILTER_DEFAULT);
                            for ($j = 0; $j < count($schedules); $j++) {
                                if (!empty($schedules[$j])) {
                                    $choice->addSlot(strip_tags($schedules[$j]));
                                }
                            }
                        }
                    }
                    $form->sortChoices();
                }

                // Display step 3
                $summary = '<ul>';
                $choices = $form->getChoices();
                foreach ($choices as $choice) {
                    $summary .= '<li>' . strftime(__('date.FULL'), $choice->getName());
                    $first = true;
                    foreach ($choice->getSlots() as $slots) {
                        $summary .= $first ? ': ' : ', ';
                        $summary .= $slots;
                        $first = false;
                    }
                    $summary .= '</li>';
                }
                $summary .= '</ul>';

                $end_date_str = utf8_encode(strftime(__('date.DATE'), $maxExpiryDate)); //textual date


                return view('create.classic.step_3', [
                    'title' => __('step_3.Removal date and confirmation (3 on 3)'),
                    'summary' => $summary,
                    'end_date_str' => $end_date_str,
                    'default_poll_duration' => config('laradate.default_poll_duration'),
                    'use_smtp' => config('laradate.use_smtp')
                ]);

            case 4:
                // Step 4 : Data prepare before insert in DB

                // Define expiration date
                $endDate = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#']]);


                if (!empty($endDate)) {
                    $registerDate = explode('/', $endDate);

                    if (is_array($registerDate) && count($registerDate) == 3) {
                        $time = mktime(0, 0, 0, $registerDate[1], $registerDate[0], $registerDate[2]);

                        if ($time < $minExpiryDate) {
                            $form->end_date = $minExpiryDate;
                        } elseif ($maxExpiryDate < $time) {
                            $form->end_date = $maxExpiryDate;
                        } else {
                            $form->end_date = $time;
                        }
                    }
                }

                if (empty($form->end_date)) {
                    // By default, expiration date is 6 months after last day
                    $form->end_date = $maxExpiryDate;
                }

                // Insert poll in database
                $ids = Poll::createPoll($form);
                $pollId = $ids[0];
                $adminPollId = $ids[1];


                // Send confirmation by mail if enabled
                if (config('laradate.use_smtp') === true) {
                    Mail::send(new PollCreated($pollId));
                    Mail::send(new PollAdminCreated($adminPollId));
                }

                // Clean Form data in $_SESSION
                session()->forget('form');

                // Redirect to poll administration
                return redirect(Utils::getPollUrl($adminPollId, true));
        }
    }
}
