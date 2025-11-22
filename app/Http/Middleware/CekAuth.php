<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Harus login
        // dd(Auth::user());
        if (!Auth::check()) {
            return redirect('/gagal')->with('error', 'Silakan login dulu.');
        } else {
            // dd(Auth::check());
            return $next($request);
        }
    }
}
