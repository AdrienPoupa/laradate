<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('lang')) {
            App::setLocale(session()->get('lang'));
        }

        // Change language when user asked for it
        if ($request->has('lang') && is_string($request->input('lang'))
            && in_array($request->input('lang'), array_keys(config('laradate.ALLOWED_LANGUAGES')))) {
            App::setLocale(request('lang'));
            session()->put('lang', $request->input('lang'));
            session()->save();
        }

        return $next($request);
    }
}
