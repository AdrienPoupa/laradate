<?php

namespace App\Http\Controllers;

use App;
use App\Poll;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $demoPoll = Poll::find('aqg259dth55iuhwm');
        $nbCol = max( config('laradate.show_what_is_that') + config('laradate.show_the_software') + config('laradate.show_cultivate_your_garden'), 1 );

        return view('index', [
            'show_what_is_that' => config('laradate.show_what_is_that'),
            'show_the_software'  => config('laradate.show_the_software'),
            'show_cultivate_your_garden'  => config('laradate.show_cultivate_your_garden'),
            'col_size' => 12 / $nbCol,
            'demo_poll'  => $demoPoll,
            'title'  => __('generic.Make your polls')
        ]);
    }
}
