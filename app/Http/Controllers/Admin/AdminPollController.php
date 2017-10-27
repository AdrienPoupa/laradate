<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Poll;
use Illuminate\Http\Request;

class AdminPollController extends Controller
{
    public function index(Request $request)
    {
        $polls = null;
        $poll_to_delete = null;

        $page = (int)filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $page = ($page >= 1) ? $page : 1;

        // Search
        $search['poll'] = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => config('laradate.POLL_REGEX')]]);
        $search['title'] = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING);
        $search['name'] = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
        $search['mail'] = filter_input(INPUT_GET, 'mail', FILTER_SANITIZE_STRING);

        // Delete poll
        if ($request->has('delete_poll')) {
            $delete_id = filter_input(INPUT_POST, 'delete_poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => config('laradate.POLL_REGEX')]]);
            $poll_to_delete = Poll::find($delete_id);
        }

        // Confirm deletion
        if ($request->has('delete_confirm')) {
            $poll_id = filter_input(INPUT_POST, 'delete_confirm', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => config('laradate.POLL_REGEX')]]);
            Poll::deleteEntirePoll($poll_id);
        }

        $found = Poll::findAllPolls($search, $page - 1, 30);
        $polls = $found['polls'];
        $count = $found['count'];
        $total = $found['total'];

        return view('admin.polls', [
            'polls' => $polls,
            'count' => $count,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($count / 30),
            'poll_to_delete' => $poll_to_delete,
            'search' => $search,
            'search_query' => $this->buildSearchQuery($search),
            'title' => __('admin.Polls'),
        ])->render();
    }

    private function buildSearchQuery($search) {
        $query = '';
        foreach ($search as $key => $value) {
            $query .= $key . '=' . urlencode($value) . '&';
        }
        return substr($query, 0, -1);
    }
}
