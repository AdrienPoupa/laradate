<?php

namespace App\Http\Controllers\Poll;

use App\Http\Controllers\Controller;
use App\Mail\SendPollList;
use App\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FindPollController extends Controller
{
    public function index()
    {
        return view('find_polls', [
            'title' => __('homepage.Where are my polls'),
        ]);
    }
    
    public function post(Request $request)
    {
        $validator = validator()->make($request->all(),[
            'mail' => 'required|email|max:128',
        ]);
        if ($validator->fails()) {
            session()->flash('danger', __('error.Something is wrong with the format'));
        } else {
            $polls = Poll::where('admin_mail', $request->input('mail'))->get();

            if (count($polls) > 0) {
                Mail::send(new SendPollList($request->input('mail'), $polls));
                if (!Mail::failures()) {
                    session()->flash('success', __('findPolls.Polls sent'));
                }
            } else {
                session()->flash('warning', __('error.No polls found'));
            }
        }

        return redirect('poll/find');
    }
}
