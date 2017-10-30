<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @internal param null|string $guard
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::user()->is_admin) {
            return redirect('/');
        }

        return $next($request);
    }
}
