<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Poll;
use Illuminate\Http\Request;

class AdminPollController extends Controller
{
    /**
     * Display the polls in the database
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $polls = Poll::findAdminPolls();

        $total = Poll::count();

        return view('admin.polls', [
            'polls' => $polls,
            'total' => $total,
            'title' => __('admin.Polls'),
        ]);
    }

    /**
     * Delete a poll
     * @param Poll $poll
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy(Poll $poll) {
        $poll->delete();

        return redirect('/admin/polls');
    }
}
