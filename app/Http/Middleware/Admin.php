<?php

namespace App\Http\Middleware;

use Closure;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // dd(Auth::user());
        if (Auth::user()) {
            if (Auth::user()->is_admin == 1) {
                return $next($request);
            }
        }
        return response(['message' => 'You don\'t have permission to perform this action'], 403);
        // try {
        // } catch (Notfo $e) {
        //     // dd($e);
        // }
    }
}
