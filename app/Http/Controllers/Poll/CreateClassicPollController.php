<?php

namespace App\Http\Controllers\Poll;

use App\Choice;
use App\Http\Controllers\Controller;
use App\Mail\PollAdminCreated;
use App\Mail\PollCreated;
use App\Poll;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CreateClassicPollController extends Controller
{
    public function index(Request $request)
    {
        $form = session()->get('form');

        // Step 1/4 : error if $_SESSION is not valid
        if (empty($form->title) || empty($form->admin_name)) {

            return response()->view('errors.error', [
                'title' => __('error.Error!'),
                'error' => __('error.You haven\'t filled the first section of the poll creation.')
            ], 403);

        } else {
            // Min/Max archive date
            $minExpiryDate = Poll::minExpiryDate();
            $maxExpiryDate = Poll::maxExpiryDate();

            // The poll format is other
            if ($form->format !== 'A') {
                $form->format = 'A';
                $form->clearChoices();
            }

            // Step 4 : Data prepare before insert in DB
            if ($request->has('confirmation')) {

                // Define expiration date
                $enddate = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#']]);

                if (!empty($enddate)) {
                    $registredate = explode('/', $enddate);

                    if (is_array($registredate) && count($registredate) == 3) {
                        $time = mktime(0, 0, 0, $registredate[1], $registredate[0], $registredate[2]);

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
                if (config('laradate.use_smtp')) {
                    Mail::send(new PollCreated($pollId));
                    Mail::send(new PollAdminCreated($adminPollId));
                }

                // Clean Form data in $_SESSION
                session()->forget('form');

                // Redirect to poll administration
                return redirect(Utils::getPollUrl($adminPollId, true));

            } // Step 3/4 : Confirm poll creation and choose a removal date
            else if ($request->has('end_other_poll')) {

                // Store choices in $_SESSION
                if ($request->has('choices')) {
                    $form->clearChoices();
                    foreach ($request->input('choices') as $c) {
                        if (!empty($c)) {
                            $c = strip_tags($c);
                            $choice = new Choice($c);
                            $form->addChoice($choice);
                        }
                    }
                }

                // Expiration date is initialised with config parameter. Value will be modified in step 4 if user has defined an other date
                $form->end_date = $maxExpiryDate;

                // Summary
                $summary = '<ol>';
                foreach ($form->getChoices() as $i => $choice) {

                    preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/', $choice->getName(), $md_a_img); // Markdown [![alt](src)](href)
                    preg_match_all('/!\[(.*?)\]\((.*?)\)/', $choice->getName(), $md_img); // Markdown ![alt](src)
                    preg_match_all('/\[(.*?)\]\((.*?)\)/', $choice->getName(), $md_a); // Markdown [text](href)
                    if (isset($md_a_img[2][0]) && $md_a_img[2][0] != '' && isset($md_a_img[3][0]) && $md_a_img[3][0] != '') { // [![alt](src)](href)

                        $li_subject_text = (isset($md_a_img[1][0]) && $md_a_img[1][0] != '') ? stripslashes($md_a_img[1][0]) : __('generic.Choice') . ' ' . ($i + 1);
                        $li_subject_html = '<a href="' . $md_a_img[3][0] . '"><img src="' . $md_a_img[2][0] . '" class="img-responsive" alt="' . $li_subject_text . '" /></a>';

                    } elseif (isset($md_img[2][0]) && $md_img[2][0] != '') { // ![alt](src)

                        $li_subject_text = (isset($md_img[1][0]) && $md_img[1][0] != '') ? stripslashes($md_img[1][0]) : __('generic.Choice') . ' ' . ($i + 1);
                        $li_subject_html = '<img src="' . $md_img[2][0] . '" class="img-responsive" alt="' . $li_subject_text . '" />';

                    } elseif (isset($md_a[2][0]) && $md_a[2][0] != '') { // [text](href)

                        $li_subject_text = (isset($md_a[1][0]) && $md_a[1][0] != '') ? stripslashes($md_a[1][0]) : __('generic.Choice') . ' ' . ($i + 1);
                        $li_subject_html = '<a href="' . $md_a[2][0] . '">' . $li_subject_text . '</a>';

                    } else { // text only

                        $li_subject_text = stripslashes($choice->getName());
                        $li_subject_html = $li_subject_text;

                    }

                    $summary .= '<li>' . $li_subject_html . '</li>' . "\n";
                }
                $summary .= '</ol>';

                $end_date_str = utf8_encode(strftime(__('date.DATE'), $maxExpiryDate)); //textual date

                return view('create.classic.step_3', [
                    'title' => __('step_3.Removal date and confirmation (3 on 3)'),
                    'summary' => $summary,
                    'end_date_str' => $end_date_str,
                    'default_poll_duration' => config('laradate.default_poll_duration'),
                    'use_smtp' => config('laradate.use_smtp')
                ]);

                // Step 2/4 : Select choices of the poll
            } else {
                $choices = $form->getChoices();
                $nbChoices = max(count($choices), 5);

                return view('create.classic.step_2', [
                    'title' => __('step_2_classic.Poll subjects (2 on 3)'),
                    'choices' => $choices,
                    'nb_choices' => $nbChoices,
                ]);
            }
        }
    }
}
