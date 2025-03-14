<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class RememberLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Set a cookie to remember the user for 2 days (2880 minutes)
            Cookie::queue('user_login', Auth::id(), 2880);
        }

        return $next($request);
    }
}