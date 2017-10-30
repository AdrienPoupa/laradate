<?php

namespace App\Http\Controllers\Poll;

use App\Http\Controllers\Controller;
use App\Poll;
use App\Utils;
use App\Vote;
use Illuminate\Support\Facades\Response;

class CreatePollCsvController extends Controller
{
    public function index($poll_id, $admin = null)
    {
        $poll = Poll::find($poll_id);

        if (!$poll || !is_null($admin) && $poll->admin_id != $admin) {
            return response()->view('errors.error', [
                'title' => __('error.This poll doesn\'t exist !')
            ], 404);
        }

        if (is_null($admin)) {
            $forbiddenBecauseOfPassword = !$poll->results_publicly_visible && !Poll::canAccess($poll);
            $resultsAreHidden = $poll->hidden;

            if ($resultsAreHidden || $forbiddenBecauseOfPassword) {
                return response()->view('errors.error', [
                    'title' => __('error.Forbidden!')
                ], 403);
            }
        }

        $slots = Vote::allSlotsByPoll($poll);
        $votes = Vote::where('poll_id', $poll_id)->orderBy('id')->get();

        ob_start();

        if ($poll->format === 'D') {
            $titles_line = ',';
            $moments_line = ',';
            foreach ($slots as $slot) {
                $title = Utils::csvEscape(strftime(__('date.date'), $slot->title));
                $moments = explode(',', $slot->moments);

                $titles_line .= str_repeat($title . ',', count($moments));
                $moments_line .= implode(',', array_map('\App\Utils::csvEscape', $moments)) . ',';
            }
            echo $titles_line . "\r\n";
            echo $moments_line . "\r\n";
        } else {
            echo ',';
            foreach ($slots as $slot) {
                echo Utils::markdown($slot->title, true) . ',';
            }
            echo "\r\n";
        }

        foreach ($votes as $vote) {
            echo Utils::csvEscape($vote->name) . ',';
            $choices = str_split($vote->choices);
            foreach ($choices as $choice) {
                switch ($choice) {
                    case 0:
                        $text = __('generic.No');
                        break;
                    case 1:
                        $text = __('generic.Ifneedbe');
                        break;
                    case 2:
                        $text = __('generic.Yes');
                        break;
                    default:
                        $text = 'unkown';
                }
                echo Utils::csvEscape($text);
                echo ',';
            }
            echo "\r\n";
        }

        $content = ob_get_clean();
        $filesize = strlen($content);
        $filename = Utils::cleanFilename($poll->title) . '.csv';

        $handle = fopen($filename, 'w+');
        fputs($handle, $content);
        fclose($handle);

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename.csv",
            'Content-Length' => $filesize,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        return response()->download($filename, $filename, $headers)->deleteFileAfterSend(true);
    }
}
