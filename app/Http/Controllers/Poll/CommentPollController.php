<?php

namespace App\Http\Controllers\Poll;

use App\Comment;
use App\Http\Controllers\Controller;
use App\Mail\SendPollNotification;
use App\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommentPollController extends Controller
{
    public function post(Request $request, Poll $poll)
    {
        $result = false;
        $comments = [];
        $admin = false;

        $pollAdmin = $request->input('poll_admin');

        if ($request->has('poll_admin') && strlen($pollAdmin) === 24
            && !empty(Poll::where('admin_id', $poll->id)->first())) {
            $admin = true;
        }

        if (!$poll) {
            session()->flash('info', __('error.This poll doesn\'t exist !'));
        } else if ($poll && !Poll::canAccess($poll) && !$admin) {
            session()->flash('info', __('error.Wrong password'));
        } else {
            $name = $request->input('name');
            $comment = $request->input('comment');

            if ($name == null) {
                session()->flash('info', __('error.The name is invalid.'));
            } else {
                // Add comment
                $newComment = new Comment();
                $newComment->poll_id = $poll->id;
                $newComment->name = $name;
                $newComment->comment = $comment;
                $result = $newComment->save();
                if ($result) {
                    session()->flash('info', __('comments.Comment added'));
                    Mail::send(new SendPollNotification($poll, SendPollNotification::ADD_COMMENT, $name));
                } else {
                    session()->flash('info', __('error.Comment failed'));
                }
            }
            $comments = Comment::where('poll_id', $poll->id)->orderBy('id')->get();
        }

        $comments_html = view('part.comments_list', [
            'comments' => $comments,
            'admin' => $admin,
            'admin_poll_id' => $poll->admin_id,
            'poll_id' => $poll->id,
            'expired' => strtotime($poll->end_date) < time()
        ]);

        $response = ['result' => $result, 'message' => session()->get('info'), 'comments' => (string) $comments_html];

        return json_encode($response);
    }
}
