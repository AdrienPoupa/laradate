<?php

namespace App\Http\Controllers\Poll;

use App\Http\Controllers\Controller;
use App\Mail\SendPollLink;
use App\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendLinkController extends Controller
{
    public function index(Request $request, Poll $poll, $editedVoteUniqueId)
    {
        $result = false;

        $validator = validator()->make($request->all(),[
            'email' => 'required|email|max:128',
        ]);

        if ($validator->fails()) {
            session()->flash('info', __('editLink.The email address is not correct.'));
        } else {
            $time = session()->get('Common.'.config('laradate.SESSION_EDIT_LINK_TIME'));

            if (!empty($time)) {
                $remainingTime = config('laradate.TIME_EDIT_LINK_EMAIL') - (time() - $time);

                if ($remainingTime > 0) {
                    session()->flash('info', __('editLink.Please wait :d seconds before we can send an email to you then try again.', ['d' => $remainingTime]));
                }
            }

            if (isset($remainingTime) && $remainingTime <= 0 || empty($time)) {
                Mail::send(new SendPollLink($request->input('email'), $poll, $editedVoteUniqueId));
                if (!Mail::failures()) {
                    session()->flash('info', __('editLink.Your reminder has been successfully sent!'));
                    $result = true;
                }
            }
        }

        $response = ['result' => $result, 'message' => session()->get('info')];

        echo json_encode($response);
    }
}
