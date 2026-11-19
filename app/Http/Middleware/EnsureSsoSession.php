<?php

namespace App\Http\Middleware;

use App\Services\KeycloakClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSsoSession
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->session()->has('sso.access_token')
            || $request->is('auth/sso', 'auth/callback', 'auth/logout', 'logout', 'logout_user')) {
            return $next($request);
        }

        try {
            $active = app(KeycloakClient::class)->isActive($request->session()->get('sso.access_token'));
            $allowed = app(\App\Services\PortalAccess::class)->allows($request->session()->get('sso.subject'));
        } catch (\Throwable $e) {
            abort(503, 'Layanan login sedang tidak tersedia. Silakan coba kembali.');
        }

        if (! $active || ! $allowed || $request->user()->user_aktif === 'NON AKTIF') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['username' => 'Sesi SSO berakhir. Silakan masuk kembali.']);
        }

        return $next($request);
    }
}