<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Poll;

class AdminPurgePollController extends Controller
{
    public function index()
    {
        return view('admin.purge', [
            'title' => __('admin.Purge'),
        ]);
    }

    public function post()
    {
        $count = Poll::purgeOldPolls();
        session()->flash('info', __('admin.Purged:') . ' ' . $count);

        return redirect('admin/purge');
    }
}
