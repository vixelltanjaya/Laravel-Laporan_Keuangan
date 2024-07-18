<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        Log::info('CheckRole middleware called. Role: ' . $role);
        if (!Auth::check() || Auth::user()->role_id != $role || Session::get('session_id') != session()->getId()) {
            return redirect('/')->withErrors('You do not have access to this page.');
        }
        return $next($request);
    }
}
