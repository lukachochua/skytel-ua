<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckUserInfoProvided
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($request->routeIs('user.info.form', 'user.info.submit', 'logout')) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user && !$user->is_info_provided) {
            return redirect()->route('user.info.form');
        }

        return $next($request);
    }
}
